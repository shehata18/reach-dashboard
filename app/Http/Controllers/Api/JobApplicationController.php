<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class JobApplicationController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_id' => 'required|exists:jobs,id',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'cover_letter' => 'nullable|string',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        try {
            // Handle file upload
            $resumePath = $request->file('resume')->store('resumes', 'public');

            $application = JobApplication::create([
                'job_id' => $request->job_id,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'cover_letter' => $request->cover_letter,
                'resume_path' => $resumePath,
                'status' => 'pending',
            ]);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Application submitted successfully',
                    'data' => $application,
                ],
                201,
            );
        } catch (\Exception $e) {
            // Delete uploaded file if there's an error
            if (isset($resumePath)) {
                Storage::disk('public')->delete($resumePath);
            }

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to submit application',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }
}
