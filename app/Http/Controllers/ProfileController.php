<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Follower;
use App\Models\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function dataAll(Request $request, $username)
    {
        $info = Client::where('username', $username)->first();
        $a = $request->user()->id;
        $b = $info->id;

        $friends = DB::table(DB::raw("(SELECT id_friend as result_id FROM friends WHERE my_id = $b
                UNION
                SELECT my_id as result_id FROM friends WHERE id_friend = $b) as list_friend"))
            ->leftJoin('clients', 'clients.id', '=', 'list_friend.result_id')
            ->select(
                'clients.username',
                'clients.fullname',
                'clients.avatar',
                'clients.id',
                'clients.nickname',
                DB::raw("CASE
                    WHEN clients.id IN (
                        SELECT id_friend as result_id FROM friends WHERE my_id = $a
                        UNION
                        SELECT my_id as result_id FROM friends WHERE id_friend = $a
                    ) THEN 'Unfriend'
                    WHEN clients.id IN (
                        SELECT my_id FROM followers WHERE id_follower = $a AND status = 0
                    ) THEN 'Confirm'
                    WHEN clients.id IN (
                        SELECT id_follower FROM followers WHERE my_id = $a AND status = 0
                    ) THEN 'Tancel'
                    WHEN clients.id = $a THEN 'Z'
                    ELSE 'Add friend'
                END AS status")
            )
            ->orderByDesc('status')
            ->get();
        $follower = DB::table('followers')
            ->leftJoin('clients', 'followers.my_id', '=', 'clients.id')
            ->select(
                'clients.username',
                'clients.fullname',
                'clients.nickname',
                'clients.avatar',
                'clients.id',
                DB::raw("
                    CASE
                        WHEN clients.id IN (
                            SELECT id_friend as result_id FROM friends WHERE my_id = $a
                            UNION
                            SELECT my_id as result_id FROM friends WHERE id_friend = $a
                        ) THEN 'Unfriend'
                        WHEN clients.id IN (
                            SELECT my_id FROM followers WHERE id_follower = $a AND status = 0
                        ) THEN 'Confirm'
                        WHEN clients.id IN (
                            SELECT id_follower FROM followers WHERE my_id = $a AND status = 0
                        ) THEN 'Tancel'
                        WHEN clients.id = $a Then 'Z'
                        ELSE 'Add friend'
                    END AS status
                ")
            )
            ->where('id_follower', $b)
            ->orderByDesc('status')
            ->get();

        return response()->json([
            'friends' => $friends,
            'followers' => $follower
        ]);
    }
}
