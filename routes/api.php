<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/sign-up', [ClientController::class, 'signUp']);
Route::post('/sign-in', [ClientController::class, 'signIn']);

// Route::get('/data', [ClientController::class, "getData"]);
Route::get('/dataPost', [PostController::class, "dataPost"]);
Route::post('/create-post', [PostController::class, "createPost"]);
