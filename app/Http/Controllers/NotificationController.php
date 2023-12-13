<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Connection;
use App\Models\Notification;
use App\Models\RequestGroup;
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
            'sender.username as username',
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
    public function infoInvite(Request $request){
        $notification = Notification::find($request->id);
        $client = Client::find($notification->my_id);
        return response()->json([
            'client'    => $client,
            'notification' => $notification
        ]);
    }
    public function acceptInvite(Request $request){
        try {
            DB::beginTransaction();
            RequestGroup::where('id_client', $request->my_id)
            ->where('id_group', $request->id_group)->where('id_invite', $request->id_client)->delete();
            Notification::find($request->id)->delete();
            Connection::create([
                'id_client'         => $request->id_client,
                'id_group'          => $request->id_group,
            ]);
            DB::commit();
            return response()->json([
                'status'    => 1,
                'message'   => 'From now on you are a member of the group',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'    => 0,
                'message'   => $th,
            ]);
        }
    }
    public function removeInvite(Request $request){
        try {
            DB::beginTransaction();
            RequestGroup::where('id_client', $request->my_id)
                ->where('id_group', $request->id_group)->where('id_invite', $request->id_client)->delete();
            Notification::find($request->id)->delete();
            DB::commit();
            return response()->json([
                'status'    => 1,
                'message'   => 'Successfully declined the invitation!',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'    => 0,
                'message'   => $th,
            ]);
        }
    }
}
