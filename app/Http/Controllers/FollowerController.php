<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Follower;
use App\Models\Friend;
use App\Models\Notification;
use Illuminate\Http\Request;

class FollowerController extends Controller
{
    public function addFriend(Request $request)
    {
        $client = $request->user();
        $check = Follower::where('my_id', $client->id)
            ->where('id_follower', $request->id)->first();
        if ($check) {
            $check->update([
                'status' => Follower::friend_request,
            ]);
            return response()->json([
                'status'    => 1,
                'message'   => 'Sent friend request successfully',
            ]);
        } else {
            $follow = Follower::create([
                'my_id' => $client->id,
                'id_follower' => $request->id,
                'status' => Follower::friend_request,
            ]);
            Notification::create([
                'id_client'             => $request->id,
                'my_id'                 => $client->id,
                'type'                  => Notification::request_friend,
            ]);
            if ($follow) {
                return response()->json([
                    'status'    => 1,
                    'message'   => 'Sent friend request successfully',
                ]);
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => 'Unavailable',
                ]);
            }
        }
    }
    public function cancelFriend(Request $request)
    {
        $client = $request->user();
        $follow = Follower::where('id_follower', $request->id)
            ->where('my_id', $client->id)->first();
        if ($follow) {
            $follow->delete();
            return response()->json([
                'status'    => 1,
            ]);
        } else {
            return response()->json([
                'status'    => 0,
            ]);
        }
    }

    public function requestFriend(Request $request)
    {
        $client = $request->user();
        // select clients.* from followers join clients on clients.id = followers.my_id
        // where followers.status = 0 and followers.id_follower = 9
        $follower = Follower::join('clients', 'clients.id', 'followers.my_id')
            ->where('id_follower', $client->id)
            ->Where('followers.status', Follower::friend_request)
            ->select('clients.*')
            ->limit(10)
            ->get();
        if ($follower) {
            return response()->json([
                'status' => 1,
                'data'  => $follower,
            ]);
        } else {
            return response()->json([
                'status'    => 0,
            ]);
        }
    }
    public function requestFriendLimit(Request $request)
    {
        $client = $request->user();
        // select clients.* from followers join clients on clients.id = followers.my_id
        // where followers.status = 0 and followers.id_follower = 9
        $count = Follower::join('clients', 'clients.id', 'followers.my_id')
            ->where('id_follower', $client->id)
            ->Where('followers.status', Follower::friend_request)
            ->select('clients.*')
            ->get();
        $total = $request->limit;
        if ($count->count() > 10) {
            $total += 10;
        }
        // select clients.* from followers join clients on clients.id = followers.my_id
        // where followers.status = 0 and followers.id_follower = 9
        $follower = Follower::join('clients', 'clients.id', 'followers.my_id')
            ->where('id_follower', $client->id)
            ->Where('followers.status', Follower::friend_request)
            ->select('clients.*')
            ->limit($total)
            ->get();
        if ($follower) {
            return response()->json([
                'status' => 1,
                'data'  => $follower,
            ]);
        } else {
            return response()->json([
                'status'    => 0,
            ]);
        }
    }
    public function acceptFriend(Request $request)
    {
        $client = $request->user();
        $confirm = Friend::create([
            'my_id'         => $client->id,
            'id_friend'     =>  $request->id,
            'status'        => Friend::friend,
        ]);
        if ($confirm) {
            $follower = Follower::where('my_id', $request->id)
                ->where('id_follower', $client->id)->first();
            if ($follower) {
                $follower->delete();
                return response()->json([
                    'status'    => 1,
                    'message'   => 'Confirm successfully',
                ]);
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => 'Erorr',
                ]);
            }
        }
    }
    public function deleteFriend(Request $request)
    {
        $client = $request->user();
        $check = Follower::where('my_id', $request->id)
            ->where('id_follower', $client->id)->first();
        if ($check) {
            $check->update([
                'status' => Follower::un_friend_request
            ]);
            return response()->json([
                'status'    => 1,
                'message' => 'Refuse successfully'
            ]);
        }
    }
}
