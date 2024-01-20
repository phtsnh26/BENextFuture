<?php

namespace App\Http\Controllers;

use App\Models\CommentGroup;
use App\Models\Group;
use App\Models\PostGroup;
use App\Models\PostGroupLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostGroupController extends Controller
{
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $client = $request->user();

            if ($request->hasFile('images') && count($request->images) > 0) {
                $arr = $request->all();
                $images = $request->file('images');
                $fileNames = [];
                foreach ($images as $image) {
                    if ($image->isValid()) {
                        $file_name = $image->getClientOriginalName();
                        $image->move(public_path('img/post'), time() . "_" . $file_name);
                        $fileNames[] = 'post/' . time() . "_" . $file_name;
                    }
                }
                $result = json_encode($fileNames, JSON_THROW_ON_ERROR);
                $arr['images'] = $result; // Thêm key 'images' với giá trị từ $result
            } else {
                $arr = $request->all(); // Loại bỏ key 'images' nếu nó tồn tại trong request
            }
            $arr['caption'] = $request->caption;
            $arr['privacy'] = $request->privacy == 'true' ? 1 : 0;
            $arr['id_client'] = $client->id;
            $group = Group::find($arr['id_group']);

            if ($group->post_approval == Group::requiredPostInGroupApproval) {
                $arr['status'] = 0;
            } else {
                $arr['status'] = 1;
            }

            $post = PostGroup::create($arr);
            // return response()->json([
            //     'status'    => 0,
            //     'message'   => 'đây',
            // ]);
            DB::commit();

            if ($post) {
                return response()->json([
                    'status'    => 1,
                    'post'      => $post,
                    'message'   => 'Posted successfully!',
                ]);
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => 'Posting error!',
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'    => 0,
                'message'   => 'Fail rồi con gà',
            ]);
        }
    }
    public function dataApprove(Request $request)
    {
        $listPost = PostGroup::leftJoin('clients', 'clients.id', 'post_groups.id_client')
            ->where('id_group', $request->id)
            ->where('post_groups.status', PostGroup::PENDING)
            ->orderByDESC('post_groups.created_at')
            ->select('post_groups.*', 'clients.fullname', 'clients.username', 'clients.avatar')
            ->get();
        return response()->json([
            'listPost'    => $listPost,
        ]);
    }
    public function data(Request $request)
    {
        $listPost = PostGroup::leftJoin('clients', 'clients.id', 'post_groups.id_client')
            ->where('post_groups.status', PostGroup::APPROVED)
            ->orderByDESC('post_groups.created_at')
            ->where('id_group', $request->id)
            ->select('post_groups.*', 'clients.fullname', 'clients.username', 'clients.avatar')
            ->get();
        foreach ($listPost as $key => $value) {
            $check = PostGroupLike::where('id_post', $value->id)->where('id_client', $request->user()->id)->first();
            $totalLikes = PostGroupLike::where('id_post', $value->id)->count();
            if ($check) {
                $listPost[$key]['liked'] = 1;
            } else {
                $listPost[$key]['liked'] = 0;
            }
            $listPost[$key]['likes'] = $totalLikes;
            $comments = CommentGroup::where('id_post', $value->id)->get();
            $listPost[$key]['comments'] = count($comments);
        }
        return response()->json([
            'listPost'    => $listPost,
        ]);
    }
    public function approve(Request $request)
    {
        $curentGroup = PostGroup::find($request->id);
        if ($curentGroup) {
            $curentGroup->status = PostGroup::APPROVED;
            $curentGroup->save();
            return response()->json([
                'status'    => 1,
                'message'   => 'Approved successfully!',
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'The post has been approved by another administrator!',
            ]);
        }
    }
    public function refuse(Request $request)
    {
        $curentGroup = PostGroup::find($request->id);
        if ($curentGroup) {
            $curentGroup->delete();
            return response()->json([
                'status'    => 1,
                'message'   => 'Refused!',
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'This post has been rejected by another admin!',
            ]);
        }
    }
    public function approveSelect(Request $request)
    {
        foreach ($request->listID as $key => $value) {

            PostGroup::find($value)->update(['status' => PostGroup::APPROVED]);
        }
        return response()->json([
            'status'    => 1,
            'message'   => 'Approved successfully!',
        ]);
    }
    public function refuseSelect(Request $request)
    {
        foreach ($request->listID as $key => $value) {

            PostGroup::find($value)->delete();
        }
        return response()->json([
            'status'    => 1,
            'message'   => 'Refused!',
        ]);
    }
    public function like(Request $request)
    {
        $check = PostGroupLike::create([
            'id_post' => $request->id,
            'id_client' => $request->user()->id,
        ]);
        if ($check) {
            return response()->json([
                'status'    => 1,
                'message'   => 'Liked!',
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Fail to like!',
            ]);
        }
    }
    public function unLike(Request $request)
    {
        PostGroupLike::where('id_client', $request->user()->id)->where('id_post', $request->id)->delete();
        return response()->json([
            'status'    => 1,
            'message'   => 'oke!',
        ]);
    }
}
