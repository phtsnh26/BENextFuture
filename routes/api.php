<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoriesController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::post('/sign-up', [ClientController::class, 'register']);
Route::post('/sign-in', [ClientController::class, 'login']);
Route::get('/authorization', [ClientController::class, 'authorization']);

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

        Route::get('/request-friend', [FollowerController::class, "requestFriend"]);    // danh sách những người gửi lời mời cho mình
        Route::post('/request-friend-limit', [FollowerController::class, "requestFriendLimit"]);
        Route::post('/accept-friend', [FollowerController::class, "acceptFriend"]);
        Route::post('/delete-friend', [FollowerController::class, "deleteFriend"]);
    });

    Route::group(['prefix' => '/{username}'], function () {
        Route::get('/data-info', [ClientController::class, "getInfo"]);
        Route::get('/data-all', [ProfileController::class, "dataAll"]);             // tất cả thông tin profile
    });
    Route::group(['prefix' => '/profile'], function () {
        Route::get('/data', [ClientController::class, "getProfile"]);               // thông tin tổng quát của profile
        Route::get('/accounts-edit', [ProfileController::class, "dataAccount"]);     // trang cá nhân của người đang đăng nhập
        Route::post('/update-profile', [ProfileController::class, "updateProfile"]);     // trang cá nhân của người đang đăng nhập
    });

    Route::get('/dataFull', [ClientController::class, "getAllData"]);               // những người bạn có thể biết
    Route::get('/data-all-friend', [FriendController::class, "getAllFriend"]);      // danh sách friend
    Route::post('/delete-friend', [FriendController::class, "delFriend"]);


    Route::group(['prefix' => '/post'], function () {
        Route::post('/create', [PostController::class, "create"]);
        Route::get('/data', [PostController::class, "dataPost"]);

        Route::post('/like', [PostLikeController::class, "like"]);                  // tim bài đăng
        Route::post('/un-like', [PostLikeController::class, "unLike"]);             // huỷ tim bài đăng
    });

    Route::group(['prefix' => '/comment'], function () {
        Route::get('/data', [CommentController::class, 'data']);
        Route::post('/create', [CommentController::class, 'store']);
    });

    Route::group(['prefix' => '/groups'], function () {
        Route::post('/create', [GroupController::class, 'createGroup']);                                // tạo nhóm mới
        Route::get('/data-discover', [GroupController::class, 'data_all_group']);                       // data tất cả nhóm chưa tham gia
        Route::get('/data-popular-group', [GroupController::class, 'dataPopularGroup']);                // data random nhóm chưa tham gia
        Route::get('/data-your-group', [GroupController::class, 'data_your_group']);                    // data nhóm bạn quản lý
        Route::get('/data-group-participated', [GroupController::class, 'data_group_participated']);    // data nhóm đang tham gia không bao gồm nhóm admin
        Route::get('/data-all-group-participated', [GroupController::class, 'dataAllGroupParticipated']);  // data toàn bộ nhóm đang tham gia
        Route::post('/data-invite', [GroupController::class, 'dataInvite']);                            // list bạn khi tạo nhóm
        Route::post('/data-invited', [GroupController::class, 'dataInvited']);                          // list lời mời của bạn
        Route::get('/{id_group}', [GroupController::class, 'infoGroup']);                               // trang chủ nhón dựa vào id
        Route::post('/data-invite-detail', [GroupController::class, 'dataInviteDetail']);               // list bạn để mời vào nhóm trừ những người đã trong nhóm
        Route::post('/send-invite', [GroupController::class, 'sendInvite']);                            // mời bạn vào nhóm
        Route::post('/come-in-group', [GroupController::class, 'comeInGroup']);                         // xin vào nhóm
        Route::post('/data-come-in-group', [GroupController::class, 'dataComeInGroup']);                // data xin vào nhóm
        Route::post('/current-group', [GroupController::class, 'getData']);                             // lấy thông tin nhóm hiện tại thông qua id
        Route::post('/update-privacy', [GroupController::class, 'updatePrivacy']);                      // cập nhật quyền riêng tư nhóm
        Route::post('/update-display', [GroupController::class, 'updateDisplay']);                      // cập nhật quyền hiển thị nhóm
        Route::post('/update-join-approval', [GroupController::class, 'updateJoinApproval']);           // cập nhật duyệt vào nhóm
        Route::post('/update-post-approval', [GroupController::class, 'updatePostApproval']);           // cập nhật duyệt đăng bài
        Route::post('/approve-connection', [ConnectionController::class, 'approveConnection']);             // phê duyệt lời mời từ Request_Group vào Connection
        Route::post('/approve-connection-all', [ConnectionController::class, 'approveConnectionAll']);      // phê duyệt tất cả lời mời từ Request_Group vào Connection
        Route::post('/refuse-connection', [ConnectionController::class, 'refuseConnection']);               // từ chối lời mời từ Request_Group và xóa khỏi bảng Request_Group
        Route::post('/refuse-connection-all', [ConnectionController::class, 'refuseConnectionAll']);        // từ chối lời mời từ Request_Group và xóa khỏi bảng Request_Group
        Route::post('/check-role', [ConnectionController::class, 'checkRole']);                         // check giao diện
        Route::post('/check-request', [ConnectionController::class, 'checkRequest']);                   // check xem đã xin vào group này chưa
        Route::post('/undo-request', [ConnectionController::class, 'undoRequest']);                     // huỷ xin vào group
        Route::post('/leave-group', [ConnectionController::class, 'leaveGroup']);                       // huỷ xin vào group

        Route::group(['prefix' => '/members'], function () {
            Route::post('/data', [GroupController::class, 'dataMember']);                               // Data tất cả member của group
            Route::post('/data-friend', [GroupController::class, 'dataMemberFriend']);                  // Data bạn bè có trong group
            Route::post('/data-admin', [GroupController::class, 'dataAdmin']);                          // Data admin của group
            Route::post('/data-moderation', [GroupController::class, 'dataModeration']);                // Data quyền Moderation thua admin trong group
            Route::post('/search-member', [MemberController::class, 'searchMember']);              // Tìm kiếm member trong group
            Route::post('/remove-member', [MemberController::class, 'removeMember']);                   // Xóa member ra khỏi group
            Route::post('/grant-permission', [MemberController::class, 'grantPermissions']);            // cấp quyền cho member trong group
            Route::post('/remove-permission', [MemberController::class, 'removePermissions']);          // xóa quyền cho member trong group
        });
    });
    Route::group(['prefix' => '/notification'], function () {
        Route::get('/data', [NotificationController::class, 'getData']);
        Route::post('/update-status', [NotificationController::class, 'updateStatus']);                // Cập nhật thông báo khi người đó đã đọc
        Route::post('/info-invite', [NotificationController::class, 'infoInvite']);                    // thông tin người gửi trong group
        Route::post('/accept-invite', [NotificationController::class, 'acceptInvite']);                // chấp nhận lời mời vào group
        Route::post('/remove-invite', [NotificationController::class, 'removeInvite']);                // Xoá lời mời vào group

    });
    Route::get('/test', [TestController::class, 'test']);

    Route::post('/upload-file', [ImageController::class, 'upload']);
    Route::post('/upload-image', [ImageController::class, 'uploadImage']);
});
