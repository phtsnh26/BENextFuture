<?php

namespace App\Http\Controllers;

use App\Models\PostLike;
use Illuminate\Http\Request;

class PostLikeController extends Controller
{
    public function like(Request $request)
    {
        $postLike = PostLike::create([
            'id_post'       => $request->id,
            'id_client'     => $request->user()->id,
        ]);
        if (!$postLike) {
            return response()->json([
                'status'    => 0,
                'message'   => 'Like failed!',
            ]);
        }
        return response()->json([
            'status'    => 1,
            'message'   => 'Liked!',
        ]);
    }

    public function unLike(Request $request){
        $check = PostLike::where('id_post', $request->id)->where('id_client', $request->user()->id)->first();
        if($check){
            $check->delete();
            return response()->json([
                'status'    => 1,
                'message'   => 'Unliked!',
            ]);
        }
        return response()->json([
            'status'    => 0,
            'message'   => 'Fail to unlike!',
        ]);
    }
}
