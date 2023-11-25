<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FriendController extends Controller
{
    public function getAllFriend(Request $request)
    {
        $client = $request->user();
        // SELECT clients.*
        // FROM (
        //     SELECT id_friend AS result_id
        //     FROM friends
        //     WHERE my_id = 1

        //     UNION

        //     SELECT my_id AS result_id
        //     FROM friends
        //     WHERE id_friend = 1
        // ) AS friend
        // JOIN clients ON clients.id = friend.result_id;

        $userId = $client->id;

        $friends = DB::table('friends')
            ->select('id_friend as result_id')
            ->where('my_id', $userId)
            ->union(
                DB::table('friends')
                    ->select('my_id as result_id')
                    ->where('id_friend', $userId)
            )
            ->get();

        $friendIds = $friends->pluck('result_id')->toArray();

        $all_friend = DB::table('clients')
            ->whereIn('id', $friendIds)
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
