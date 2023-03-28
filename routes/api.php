<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\GlobalController;
use App\Http\Controllers\Api\CommentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/get-random-users', [GlobalController::class, 'getRandomUsers']);
Route::get('/posts', [PostController::class, 'index']);


Route::middleware(['auth:sanctum'])->group(function () {
  Route::get('/user', [UserController::class, 'getAuthUser']);
  Route::post('/user/update', [UserController::class, 'update']);
  Route::post('/user/update-image', [UserController::class, 'updateUserImage']);

  Route::get('/posts/{id}', [PostController::class, 'show']);
  Route::post('/posts', [PostController::class, 'store']);
  Route::delete('/posts/{id}', [PostController::class, 'destroy']);

  Route::get('/profile/{id}', [UserController::class, 'show']);

  Route::post('/comments', [CommentController::class, 'store']);
  Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

  Route::post('/likes', [LikeController::class, 'store']);
  Route::delete('/likes/{id}', [LikeController::class, 'destroy']);
});
