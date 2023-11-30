<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoriesController;
use App\Http\Controllers\ImageController;


Route::post('/sign-up', [ClientController::class, 'register']);
Route::post('/sign-in', [ClientController::class, 'login']);



Route::middleware('auth:sanctum')->group(function () {

    Route::get('/sign-out', [ClientController::class, 'signOut']);

    Route::group(['prefix' => '/story'], function () {
        Route::get('/data', [StoriesController::class, "getStory"]);
        Route::get('/data-all', [StoriesController::class, "getAllStory"]);
        Route::get('/{id}', [StoriesController::class, 'detailStory']);
        Route::post('/create', [StoriesController::class, 'store']);
    });
    Route::group(['prefix' => '/follower'], function () {
        Route::post('/add-friend', [FollowerController::class, "addFriend"]);
        Route::post('/cancel-friend', [FollowerController::class, "cancelFriend"]);

        Route::get('/request-friend', [FollowerController::class, "requestFriend"]);
        Route::post('/request-friend-limit', [FollowerController::class, "requestFriendLimit"]);
        Route::post('/accept-friend', [FollowerController::class, "acceptFriend"]);
        Route::post('/delete-friend', [FollowerController::class, "deleteFriend"]);
    });

    Route::group(['prefix' => '/{username}'], function () {
        Route::get('/data-info', [ClientController::class, "getInfo"]);
    });
    Route::group(['prefix' => '/profile'], function () {
        Route::get('/data', [ClientController::class, "getProfile"]);
    });

    Route::get('/dataFull', [ClientController::class, "getAllData"]);
    Route::get('/data-all-friend', [FriendController::class, "getAllFriend"]);
    Route::post('/delete-friend', [FriendController::class, "delFriend"]);


    Route::group(['prefix' => '/post'], function () {
        Route::post('/create', [PostController::class, "create"]);
        Route::get('/data', [PostController::class, "dataPost"]);
    });

    Route::group(['prefix' => '/comment'], function () {
        Route::get('/data', [CommentController::class, 'data']);
        Route::post('/create', [CommentController::class, 'store']);
    });

    Route::group(['prefix' => '/groups'], function () {
        Route::get('/data-discover', [GroupController::class, 'data_all_group']);                     // data  tất cả nhóm
        Route::get('/data-your-group', [GroupController::class, 'data_your_group']);                  // data nhóm của người đang đăng nhập tạo
        Route::get('/data-group-participated', [GroupController::class, 'data_group_participated']);  // data nhóm đang tham gia
        Route::post('/create', [GroupController::class, 'createGroup']);                              // tạo nhóm mới
    });


    Route::post('/upload-file', [ImageController::class, 'upload']);
    Route::post('/upload-image', [ImageController::class, 'uploadImage']);
});
