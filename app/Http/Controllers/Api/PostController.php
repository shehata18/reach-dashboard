<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Post::query();

        // Base visibility filter
        if (!$request->boolean('include_unpublished', false)) {
            $query->where('is_published', true)
                  ->whereNotNull('published_at')
                  ->where('published_at', '<=', now());
        }

        // Search functionality
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('content', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Date range filtering
        if ($request->has('date_from')) {
            $query->whereDate('published_at', '>=', $request->input('date_from'));
        }
        if ($request->has('date_to')) {
            $query->whereDate('published_at', '<=', $request->input('date_to'));
        }

        // Filter by year and month
        if ($request->has('year')) {
            $query->whereYear('published_at', $request->input('year'));
            
            if ($request->has('month')) {
                $query->whereMonth('published_at', $request->input('month'));
            }
        }

        // Sorting
        $sortField = $request->input('sort_by', 'published_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $allowedSortFields = ['title', 'published_at', 'created_at'];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $posts = $perPage === 'all' ? $query->get() : $query->paginate($perPage);

        // Get archive data for sidebar
        $archiveData = Post::where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->selectRaw('YEAR(published_at) as year, MONTH(published_at) as month, COUNT(*) as post_count')
            ->groupBy('year', 'month')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        return response()->json([
            'success' => true,
            'data' => PostResource::collection($posts),
            'meta' => $perPage === 'all' ? null : [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
            'archive' => $archiveData,
            'stats' => [
                'total_posts' => Post::where('is_published', true)->count(),
                'posts_this_month' => Post::where('is_published', true)
                    ->whereYear('published_at', now()->year)
                    ->whereMonth('published_at', now()->month)
                    ->count(),
            ],
        ]);
    }

    public function show($slug): JsonResponse
    {
        $query = Post::where('slug', $slug);

        if (!request()->boolean('include_unpublished', false)) {
            $query->where('is_published', true)
                  ->whereNotNull('published_at')
                  ->where('published_at', '<=', now());
        }

        $post = $query->firstOrFail();

        // Get previous and next posts
        $previousPost = Post::where('is_published', true)
            ->where('published_at', '<', $post->published_at)
            ->orderByDesc('published_at')
            ->first(['id', 'title', 'slug']);

        $nextPost = Post::where('is_published', true)
            ->where('published_at', '>', $post->published_at)
            ->orderBy('published_at')
            ->first(['id', 'title', 'slug']);

        return response()->json([
            'success' => true,
            'data' => new PostResource($post),
            'navigation' => [
                'previous' => $previousPost ? [
                    'title' => $previousPost->title,
                    'slug' => $previousPost->slug,
                ] : null,
                'next' => $nextPost ? [
                    'title' => $nextPost->title,
                    'slug' => $nextPost->slug,
                ] : null,
            ],
        ]);
    }

    public function latest(): JsonResponse
    {
        $posts = Post::where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => PostResource::collection($posts),
        ]);
    }

    public function archive($year, $month = null): JsonResponse
    {
        $query = Post::where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->whereYear('published_at', $year);

        if ($month) {
            $query->whereMonth('published_at', $month);
        }

        $posts = $query->latest('published_at')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => PostResource::collection($posts),
            'meta' => [
                'year' => $year,
                'month' => $month,
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }
}
