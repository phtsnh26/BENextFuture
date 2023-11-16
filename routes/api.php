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

    Route::get('/sign-out', [ClientController::class, 'signOut']);

    Route::group(['prefix'=> '/story'], function () {
        Route::get('/data', [StoriesController::class, "getStory"]);
        Route::get('/data-all', [StoriesController::class, "getAllStory"]);
        Route::get('/{id}', [StoriesController::class, 'detailStory']);
        Route::post('/create', [StoriesController::class, 'store']);
    });

    Route::group(['prefix' => '/profile'], function () {
        Route::get('/data', [ClientController::class, "getProfile"]);
    });

    Route::group(['prefix' => '/post'], function () {
        Route::post('/create', [PostController::class, "create"]);
        Route::get('/data', [PostController::class, "dataPost"]);
    });


    Route::post('/upload-file', [ImageController::class, 'upload']);
    Route::post('/upload-image', [ImageController::class, 'uploadImage']);
});

