<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function getData(Request $request)
    {
        $client = $request->user();
        $data = Notification::select(
            'notifications.*',
            'sender.fullname as sender',
            'sender.avatar',
            'group.cover_image',
            'group.group_name',
            'post.id_client as id_poster',
            'receiver.fullname as receiver',
            'name_poster.fullname as name_poster'
        )
        ->leftJoin('clients as sender', 'sender.id', '=', 'notifications.my_id')
        ->leftJoin('groups as group', 'group.id', '=', 'notifications.id_group')
        ->leftJoin('posts as post', 'post.id', '=', 'notifications.id_post')
        ->leftJoin('clients as receiver', 'receiver.id', '=', 'notifications.id_client')
        ->leftJoin('clients as name_poster', 'name_poster.id', '=', 'post.id_client')
        ->where('notifications.id_client', $client->id)
        ->orderBy('notifications.created_at', 'desc')
        ->get();


        return response()->json([
            'data' => $data,
        ]);
    }
}
