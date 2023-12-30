<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FriendController extends Controller
{
    public function getAllFriend(Request $request)
    {
        $client = $request->user();
        $id_friends = Friend::where('my_id', $client->id)
            ->select('id_friend as result_id')
            ->union(
                Friend::where('id_friend', $client->id)
                    ->select('my_id as result_id')
            )->pluck('result_id')->toArray();
        $all_friend = Client::whereIn('id', $id_friends)->get();
        foreach ($all_friend as $key => $value) {
            $mutual = array_intersect(Client::getFriend($value['id']), Client::getFriend($client->id));
            $all_friend[$key]->mutual = count($mutual);
        }
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
    public function delFriend(Request $request)
    {
        $client = $request->user();
        $friend = Friend::where(function ($query) use ($client, $request) {
            $query->where('my_id', $client->id)
                ->where('id_friend', $request->id);
        })
            ->orWhere(function ($query) use ($client, $request) {
                $query->where('my_id', $request->id)
                    ->where('id_friend', $client->id);
            })
            ->first();

        if ($friend) {
            $friend->delete();
            return response()->json([
                'status'  => 1,
                'message' => 'Delete friend successfully',
            ]);
        } else {
            return response()->json([
                'status'  => 0,
                'message' => 'Friend not found or could not be deleted',
            ]);
        }
    }
}
