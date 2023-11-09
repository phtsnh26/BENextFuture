<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoriesController;
use App\Http\Controllers\ImageController;


Route::post('/sign-up', [ClientController::class, 'register']);
Route::post('/sign-in', [ClientController::class, 'login']);



Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix'=> '/story'], function () {
        Route::get('/', [StoriesController::class, "getStory"]);
        Route::post('/create', [StoriesController::class, 'store']);
    });

    Route::post('/create-post', [PostController::class, "createPost"]);
    Route::get('/dataPost', [PostController::class, "dataPost"]);
    
    Route::post('/upload-file', [ImageController::class, 'upload']);
    Route::post('/upload-image', [ImageController::class, 'uploadImage']);
});

