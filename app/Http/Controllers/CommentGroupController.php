<?php

namespace App\Http\Controllers;

use App\Models\CommentGroup;
use App\Models\CommentGroupLike;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentGroupController extends Controller
{
    public function data(Request $request)
    {
        $data = CommentGroup::select('comment_groups.*', 'clients.fullname', 'clients.avatar', 'clients.username', DB::raw('COUNT(comment_group_likes.id) as likes'))
            ->leftJoin('clients', 'clients.id', '=', 'comment_groups.id_client')
            ->leftJoin('comment_group_likes', 'comment_group_likes.id_comment', '=', 'comment_groups.id')
            ->where('comment_groups.id_post', '=', $request->id)
            ->groupBy(
                'comment_groups.id',
                'comment_groups.content',
                'comment_groups.id_tag',
                'comment_groups.id_client',
                'comment_groups.id_replier',
                'comment_groups.id_post',
                'clients.fullname',
                'clients.avatar',
                'clients.username',
                'comment_groups.created_at',
                'comment_groups.updated_at'
            )
            ->orderBy('comment_groups.created_at', 'DESC')
            ->get();

        foreach ($data as $key => $value) {
            $check = CommentGroupLike::where('id_comment', $value->id)->where('id_client', $request->user()->id)->first();
            if ($check) {
                $data[$key]->liked = 1;
            } else {
                $data[$key]->liked = 0;
            }
            $rep = CommentGroup::where('id_replier', $value->id)->get();
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

        $client = $request->user();
        $data = $request->all();
        $data['id_client'] = $client->id;
        $check = CommentGroup::Create($data);
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

    public function dataReply(Request $request)
    {
        $data = CommentGroup::where('id_replier', $request->id)
            ->leftJoin('clients', 'clients.id', 'comment_groups.id_client')
            ->leftJoin('comment_group_likes', 'comment_group_likes.id_comment', '=', 'comment_groups.id')
            ->select('comment_groups.*', 'clients.fullname', 'clients.avatar', 'clients.username', DB::raw('COUNT(comment_group_likes.id) as likes'))
            ->orderByDESC('comment_groups.created_at')
            ->groupBy(
                'comment_groups.id',
                'comment_groups.content',
                'comment_groups.id_tag',
                'comment_groups.id_client',
                'comment_groups.id_replier',
                'comment_groups.id_post',
                'clients.fullname',
                'clients.avatar',
                'clients.username',
                'comment_groups.created_at',
                'comment_groups.updated_at'
            )
            ->get();
        foreach ($data as $key => $value) {
            $check = CommentGroupLike::where('id_comment', $value->id)->where('id_client', $request->user()->id)->first();
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
    public function like(Request $request)
    {
        $check = CommentGroupLike::create([
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
        $check = CommentGroupLike::where('id_comment', $request->id)->where('id_client', $request->user()->id)->first();
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
}
