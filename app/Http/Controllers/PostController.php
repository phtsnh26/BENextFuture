<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{

    public function createPost(Request $request)
    {
        $client = $request->user();
        $data = Post::create([
            'caption' => $request->caption,
            'images' => $request->images,
            'video' => $request->video,
            'is_view_like' => 1,
            'is_view_comment' => 1,
            'id_client' => $client->id,
            'id_tag' => 2,
        ]);
        if ($data) {
            return response()->json([
                'status'    => 1,
                'message'   => 'Post successfully !',
            ]);
        }
    }
}
