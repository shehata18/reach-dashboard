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
        $query = Service::query();

        // Base filters
        if (!$request->boolean('include_inactive', false)) {
            $query->where('is_active', true);
        }

        // Search by title or description
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Filter by features
        if ($request->has('features')) {
            $features = explode(',', $request->input('features'));
            $query->where(function ($q) use ($features) {
                foreach ($features as $feature) {
                    $q->whereJsonContains('features', trim($feature));
                }
            });
        }

        // Projects relationship handling
        if ($request->boolean('with_projects')) {
            $query->with([
                'projects' => function ($q) use ($request) {
                    $q->where('is_active', true);

                    // Filter projects by completion date
                    if ($request->has('project_date_from')) {
                        $q->whereDate('completion_date', '>=', $request->input('project_date_from'));
                    }
                    if ($request->has('project_date_to')) {
                        $q->whereDate('completion_date', '<=', $request->input('project_date_to'));
                    }

                    // Default project sorting
                    $q->orderBy('sort_order');
                },
            ]);
        }

        // Count related projects
        $query->withCount([
            'projects' => function ($q) {
                $q->where('is_active', true);
            },
        ]);

        // Sorting
        $sortField = $request->input('sort_by', 'sort_order');
        $sortDirection = $request->input('sort_direction', 'asc');
        $allowedSortFields = ['title', 'sort_order', 'created_at', 'projects_count'];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $services = $perPage === 'all' ? $query->get() : $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => ServiceResource::collection($services),
            'meta' =>
                $perPage === 'all'
                    ? null
                    : [
                        'current_page' => $services->currentPage(),
                        'last_page' => $services->lastPage(),
                        'per_page' => $services->perPage(),
                        'total' => $services->total(),
                    ],
        ]);
    }

    public function show($slug): JsonResponse
    {
        $query = Service::where('slug', $slug);

        $service = $query
            ->with([
                'projects' => function ($q) {
                    $q->where('is_active', true)->orderBy('sort_order');
                },
            ])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => new ServiceResource($service),
        ]);
    }

    public function features(): JsonResponse
    {
        $features = Service::where('is_active', true)->pluck('features')->flatten()->unique()->values();

        return response()->json([
            'success' => true,
            'data' => $features,
        ]);
    }

    public function stats(): JsonResponse
    {
        $stats = [
            'total_services' => Service::where('is_active', true)->count(),
            'total_projects' => Service::where('is_active', true)
                ->withCount([
                    'projects' => function ($q) {
                        $q->where('is_active', true);
                    },
                ])
                ->get()
                ->sum('projects_count'),
            'services_by_project_count' => Service::where('is_active', true)
                ->withCount([
                    'projects' => function ($q) {
                        $q->where('is_active', true);
                    },
                ])
                ->orderByDesc('projects_count')
                ->get(['title', 'projects_count']),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
