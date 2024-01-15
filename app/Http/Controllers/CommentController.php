<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{


    public function data(Request $request)
    {
        $data = Comment::select('comments.*', 'clients.fullname', 'clients.avatar', 'clients.username', DB::raw('COUNT(comment_likes.id) as likes'))
            ->leftJoin('clients', 'clients.id', '=', 'comments.id_client')
            ->leftJoin('comment_likes', 'comment_likes.id_comment', '=', 'comments.id')
            ->where('comments.id_post', '=', $request->id)
            ->groupBy(
                'comments.id',
                'comments.content',
                'comments.likes',
                'comments.id_tag',
                'comments.id_client',
                'comments.id_replier',
                'comments.id_post',
                'clients.fullname',
                'clients.avatar',
                'clients.username',
                'comments.created_at',
                'comments.updated_at'
            )
            ->orderBy('comments.created_at', 'DESC')
            ->get();

        foreach ($data as $key => $value) {
            $check = CommentLike::where('id_comment', $value->id)->where('id_client', $request->user()->id)->first();
            if ($check) {
                $data[$key]->liked = 1;
            } else {
                $data[$key]->liked = 0;
            }
            $rep = Comment::where('id_replier', $value->id)->get();
            $data[$key]->replies = count($rep);
        }
        // $rep = Comment::select('id_replier', DB::raw('COUNT(*) as rep'))
        //     ->where('id_replier', '!=', null)
        //     ->groupBy('id_replier');

        return response()->json([
            'dataComment'    => $data,
        ]);
    }

    public function store(Request $request)
    {
        // return response()->json([
        //     'status'    => $request->all(),
        // ]);

        $client = $request->user();
        $data = $request->all();
        $data['id_client'] = $client->id;
        $check = Comment::Create($data);
        $id_tags = explode(",", $request->id_tag);
        if ($request->id_tag) {
            foreach ($id_tags as $key => $value) {
                Notification::create([
                    'id_client'     => $value,
                    'my_id'         => $check->id_client,
                    'id_post'       => $request->id_post,
                    'type'          => Notification::tag_comment,
                ]);
            }
        }
        if ($check) {
            return response()->json([
                'status'    => 1,
                'message'   => 'commented successfully!',
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'comment error!',
            ]);
        }
    }
    public function like(Request $request)
    {
        $check = CommentLike::create([
            'id_client' => $request->user()->id,
            'id_comment' => $request->id
        ]);
        if (!$check) {
            return response()->json([
                'status'    => 0,
                'message'   => 'Like error!',
            ]);
        }
        return response()->json([
            'status'    => 1,
            'message'   => 'liked comment successfully!',
        ]);
    }
    public function  unLike(Request $request)
    {
        $check = CommentLike::where('id_comment', $request->id)->where('id_client', $request->user()->id)->first();
        if ($check) {
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
    public function dataReply(Request $request)
    {
        $data = Comment::where('id_replier', $request->id)
            ->leftJoin('clients', 'clients.id', 'comments.id_client')
            ->leftJoin('comment_likes', 'comment_likes.id_comment', '=', 'comments.id')
            ->select('comments.*', 'clients.fullname', 'clients.avatar', 'clients.username', DB::raw('COUNT(comment_likes.id) as likes'))
            ->orderByDESC('comments.created_at')
            ->groupBy(
                'comments.id',
                'comments.content',
                'comments.likes',
                'comments.id_tag',
                'comments.id_client',
                'comments.id_replier',
                'comments.id_post',
                'clients.fullname',
                'clients.avatar',
                'clients.username',
                'comments.created_at',
                'comments.updated_at'
            )
            ->get();
        foreach ($data as $key => $value) {
            $check = CommentLike::where('id_comment', $value->id)->where('id_client', $request->user()->id)->first();
            if ($check) {
                $data[$key]->liked = 1;
            } else {
                $data[$key]->liked = 0;
            }
        }
        return response()->json([
            'dataReply'    => $data,
        ]);
    }
}
