<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class JobController extends Controller
{
    public function index()
    {
        try {
            $jobs = Job::latest()->get();
            return response()->json([
                'status' => 'success',
                'data' => $jobs
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch jobs',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function active()
    {
        try {
            $jobs = Job::active()
                ->notExpired()
                ->positionsAvailable()
                ->latest()
                ->get();
            return response()->json([
                'status' => 'success',
                'data' => $jobs
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch active jobs',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function show($slug)
    {
        try {
            $job = Job::where('slug', $slug)->firstOrFail();
            return response()->json([
                'status' => 'success',
                'data' => $job
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Job not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch job',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
