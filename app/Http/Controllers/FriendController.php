<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Friend;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    public function getAllFriend(Request $request)
    {
        $client = $request->user();
        //SELECT clients.* FROM `friends`
        // join clients on clients.id = friends.id_friend
        // where my_id = 5
        $all_friend = Friend::where('my_id', $client->id)
            ->select('id_friend as result_id')
            ->union(
                Friend::where('id_friend', $client->id)
                    ->select("my_id as result_id")
            )->get();

        $id_friends = Friend::where('my_id', $client->id)
        ->select('id_friend as result_id')
        ->union(
            Friend::where('id_friend', $client->id)
            ->select('my_id as result_id')
        );

        $all_friend = Client::joinSub($id_friends, 'friends', function ($join) {
                $join->on('clients.id', '=', 'friends.result_id');
            })
            ->select('clients.*')
            ->get();

        if ($all_friend) {
            return response()->json([
                'status'    => 1,
                'data'      => $all_friend,
            ]);
        } else {
            return response()->json([
                'status'    => 0,
            ]);
        }
    }
}
