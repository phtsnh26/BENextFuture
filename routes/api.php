<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoriesController;
use App\Http\Controllers\ImageController;


Route::post('/sign-up', [ClientController::class, 'signUp']);
Route::post('/sign-in', [ClientController::class, 'signIn']);

// Route::get('/data', [ClientController::class, "getData"]);
Route::get('/dataPost', [PostController::class, "dataPost"]);
Route::post('/create-post', [PostController::class, "createPost"]);

Route::post('/upload-file', [ImageController::class, 'upload']);
Route::post('/upload-image', [ImageController::class, 'uploadImage']);

Route::post('/story/create', [StoriesController::class, 'store']);


