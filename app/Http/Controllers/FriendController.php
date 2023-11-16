<?php

namespace App\Http\Controllers;

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
        $all_friend = Friend::join('clients', 'clients.id', 'friends.id_friend')
            ->where('my_id', $client->id)
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
