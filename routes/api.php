<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserPreferenceController;
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
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('articles', [ArticleController::class, 'index']);       // Fetch all articles with pagination
Route::get('articles/search', [ArticleController::class, 'search']); // Search and filter articles
Route::get('articles/{id}', [ArticleController::class, 'show']);    // Get a single article
Route::middleware('auth:sanctum')->group(function () {
    Route::post('user/preferences', [UserPreferenceController::class, 'setPreferences']); // Set user preferences
    Route::get('user/preferences', [UserPreferenceController::class, 'getPreferences']);  // Get user preferences
    Route::get('user/news-feed', [ArticleController::class, 'getPersonalizedNewsFeed']); // Get personalized news feed
});