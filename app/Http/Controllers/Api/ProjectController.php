<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * List all active projects
     */
    public function index(Request $request): JsonResponse
    {
        $query = Project::query()->where('is_active', true);

        // Filter by service
        if ($request->has('service')) {
            $query->where('service_id', $request->service);
        }

        // Filter by featured status
        if ($request->has('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%");
            });
        }

        // Filter by technologies
        if ($request->has('technologies')) {
            $technologies = $request->technologies;
            $query->where(function ($q) use ($technologies) {
                foreach ($technologies as $tech) {
                    $q->whereJsonContains('technologies', $tech);
                }
            });
        }

        // Sorting
        $sortField = $request->input('sort', 'sort_order');
        $sortOrder = $request->input('order', 'asc');

        if (in_array($sortField, ['title', 'created_at', 'completion_date', 'sort_order'])) {
            $query->orderBy($sortField, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $projects = $query->paginate($perPage);

        return response()->json(
            ProjectResource::collection($projects)->additional([
                'meta' => [
                    'total_featured' => Project::where('is_featured', true)->count(),
                    'total_technologies' => Project::distinct()->whereNotNull('technologies')->pluck('technologies')->flatten()->unique()->values(),
                ],
            ]),
        );
    }

    /**
     * Get specific project details
     */
    public function show($slug): JsonResponse
    {
        $project = Project::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return response()->json([
            'data' => new ProjectResource($project),
        ]);
    }

    /**
     * Get featured projects
     */
    public function featured(): JsonResponse
    {
        $projects = Project::where('is_active', true)->where('is_featured', true)->orderBy('sort_order')->get();

        return response()->json([
            'data' => ProjectResource::collection($projects),
        ]);
    }

    /**
     * Get projects by service
     */
    public function byService($serviceId): JsonResponse
    {
        $projects = Project::where('is_active', true)->where('service_id', $serviceId)->orderBy('sort_order')->get();

        return response()->json([
            'data' => ProjectResource::collection($projects),
        ]);
    }
}
