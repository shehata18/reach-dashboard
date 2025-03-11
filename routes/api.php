<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TeamMemberController;
use App\Http\Controllers\Api\ContactSubmissionController;
use App\Http\Controllers\Api\ServiceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Projects
Route::prefix('projects')->group(function () {
    Route::get('/', [ProjectController::class, 'index']);
    Route::get('/featured', [ProjectController::class, 'featured']);
    Route::get('/by-service/{serviceId}', [ProjectController::class, 'byService']);
    Route::get('/{slug}', [ProjectController::class, 'show']);
});

// Posts
Route::get('posts', [PostController::class, 'index']);
Route::get('posts/{slug}', [PostController::class, 'show']);

// Team Members
Route::get('team', [TeamMemberController::class, 'index']);
Route::get('team/{id}', [TeamMemberController::class, 'show']);

// Contact Submission
Route::post('contact', [ContactSubmissionController::class, 'store']);

// Services
Route::get('services', [ServiceController::class, 'index']);
Route::get('services/{slug}', [ServiceController::class, 'show']);
