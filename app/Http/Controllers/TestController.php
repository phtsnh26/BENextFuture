<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(Request $request){
        $my_id = 29;
        $id_follower = 1;
        $check = Follower::where(function ($query) use ($my_id, $id_follower) {
            $query->where('my_id', $my_id)
                ->where('id_follower', $id_follower);
        })
            ->orWhere(function ($query) use ($my_id, $id_follower) {
                $query->where('my_id', $id_follower)
                    ->where('id_follower', $my_id);
            })
            ->get();
            return response()->json([
                'status'    => $check,
                'message'   => 'oke',
            ]);
    }
}
