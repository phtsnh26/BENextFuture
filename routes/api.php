<?php

use App\Http\Controllers\ImageController;
use App\Http\Controllers\StoriesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/upload-file', [ImageController::class, 'upload']);
Route::post('/upload-image', [ImageController::class, 'uploadImage']);


Route::post('/story/create', [StoriesController::class, 'store']);



// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
