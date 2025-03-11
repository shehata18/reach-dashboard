<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::where('is_published', true)->whereNotNull('published_at')->where('published_at', '<=', now())->latest('published_at')->get();

        return PostResource::collection($posts);
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->where('is_published', true)->whereNotNull('published_at')->where('published_at', '<=', now())->firstOrFail();

        return new PostResource($post);
    }
}
