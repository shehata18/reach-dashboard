<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ContactSubmissionRequest;
use App\Models\ContactSubmission;
use Illuminate\Http\JsonResponse;

class ContactSubmissionController extends Controller
{
    public function store(ContactSubmissionRequest $request): JsonResponse
    {
        try {
            $submission = ContactSubmission::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'subject' => $request->subject,
                'message' => $request->message,
                'status' => 'new',
            ]);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Thank you for your message. We will get back to you soon.',
                    'data' => [
                        'submission_id' => $submission->id,
                        'submitted_at' => $submission->created_at,
                    ],
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Sorry, there was an error submitting your message. Please try again later.',
                ],
                500,
            );
        }
    }
}
