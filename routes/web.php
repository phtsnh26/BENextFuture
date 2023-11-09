<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});
Route::get('/img/{image}', [ImageController::class, 'show'])->where('image', '.*');

// Route::post('/api/sign-up', [ClientController::class, 'signUp']);
// Route::post('/api/sign-in', [ClientController::class, 'signIn']);
