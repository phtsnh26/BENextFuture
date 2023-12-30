<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Follower;
use App\Models\Friend;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function dataAll($username)
    {
        $info = Client::where('username', $username)->first();
        $id_friend = Friend::where('my_id', $info->id)
            ->select('id_friend as result_id')
            ->Union(
                Friend::where('id_friend', $info->id)
                    ->select('my_id as result_id')
            )
            ->pluck('result_id')->toArray();
        $friends = Client::whereIn('id', $id_friend)->get();
        $follower = Follower::leftJoin('clients', 'followers.my_id', 'clients.id')
            ->where('id_follower', $info->id)
            ->select('clients.*')
            ->get();
        return response()->json([
            'friends' => $friends,
            'followers' => $follower
        ]);
    }
}
