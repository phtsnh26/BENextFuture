<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Connection;
use App\Models\Group;
use App\Models\Notification;
use App\Models\RequestGroup;
use App\Models\Role;
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
            ->orderBy('notifications.updated_at', 'desc')
            ->get();
        $new_notification = Notification::where('id_client', $client->id)
            ->where('status', 1)
            ->get()
            ->count();

        return response()->json([
            'data' => $data,
            'new_notification' => $new_notification
        ]);
    }
    public function infoInvite(Request $request)
    {
        $notification = Notification::find($request->id);
        // $client = Client::find($notification->my_id);
        $client = RequestGroup::leftJoin('clients', 'clients.id', 'request_groups.id_client')
            ->select('request_groups.updated_at as time', 'clients.*')
            ->where('id_client', $notification->my_id)
            ->where('id_invite', $notification->id_client)
            ->first();
        return response()->json([
            'client'    => $client,
            'notification' => $notification
        ]);
    }
    public function acceptInvite(Request $request)
    {
        try {
            DB::beginTransaction();
            Notification::where('id_client', $request->id_client)
                ->where('my_id', $request->my_id)
                ->where('id_group', $request->id_group)
                ->delete();
            if (Group::find($request->id_group)->join_approval == Group::requiredGroupApproval) {
                $oke = RequestGroup::where('id_client', $request->my_id)
                    ->where('id_group', $request->id_group)->where('id_invite', $request->id_client)->first();
                $oke->update([
                    'status'    => RequestGroup::come
                ]);
            } else {
                RequestGroup::where('id_client', $request->my_id)
                    ->where('id_group', $request->id_group)->where('id_invite', $request->id_client)->delete();
                Connection::create([
                    'id_client'         => $request->id_client,
                    'id_group'          => $request->id_group,
                ]);
            }
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
    public function removeInvite(Request $request)
    {
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
    public function updateStatus(Request $request)
    {
        $notification = Notification::find($request->id);
        $notification->update([
            'status'    => 0
        ]);
        return response()->json([
            'status'    => $notification
        ]);
    }
}
