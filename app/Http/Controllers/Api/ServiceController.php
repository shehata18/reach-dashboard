<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Service::where('is_active', true)->orderBy('sort_order')->withCount('projects');

        if ($request->boolean('with_projects')) {
            $query->with([
                'projects' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                },
            ]);
        }

        $services = $query->get();

        return response()->json([
            'success' => true,
            'data' => ServiceResource::collection($services),
        ]);
    }

    public function show($slug): JsonResponse
    {
        $service = Service::where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'projects' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                },
            ])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => new ServiceResource($service),
        ]);
    }
}
