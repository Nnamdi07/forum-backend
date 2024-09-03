<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Feed\FeedController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']); 


Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/feed/store', [FeedController::class, 'store']);
    Route::post('/feed/like/{id}', [FeedController::class, 'likePost']);
    Route::delete('/feed/delete/{id}', [FeedController::class, 'deletePost']);
    Route::get('/feed/show/{id}', [FeedController::class,'showFeed']);
    Route::get('/feed/all', [FeedController::class,'showAllFeeds']);
    Route::get('/feed/user/{userId}', [FeedController::class,'showUserFeeds']);
    Route::post('/feed/{id}/comment', [FeedController::class,'commentAction']);
    Route::get('/feed/{id}/comments', [FeedController::class,'getComments']);
});
