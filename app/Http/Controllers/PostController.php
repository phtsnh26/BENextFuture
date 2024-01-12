<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\Friend;
use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function create(Request $request)
    {
        $client = $request->user('');

        if ($request->hasFile('images') && count($request->images) > 0) {
            $images = $request->file('images');
            $fileNames = [];
            foreach ($images as $image) {
                if ($image->isValid()) {
                    $file_name = $image->getClientOriginalName();
                    $image->move(public_path('img/post'), time() . "_" . $file_name);
                    $fileNames[] = 'post/'. time() . "_" . $file_name;
                }
            }
            $result = json_encode($fileNames, JSON_THROW_ON_ERROR);
            $arr['images'] = $result; // Thêm key 'images' với giá trị từ $result
        } else {
            $arr = $request->all(); // Loại bỏ key 'images' nếu nó tồn tại trong request
        }
        $arr['caption'] = $request->caption;
        $arr['privacy'] = $request->privacy;
        $arr['id_client'] = $client->id;
        $post = Post::create($arr);
        if ($post) {
            return response()->json([
                'status'    => 1,
                'message'   => 'Posted successfully!',
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Posting error!',
            ]);
        }
    }
    public function dataPost(Request $request)
    {
        $client = $request->user();
        $id_client = $client->id;
        $friends = Friend::where('my_id', $client->id)
            ->select('id_friend as result_id')
            ->union(
                Friend::where('id_friend', $client->id)
                    ->select("my_id as result_id")
            )
            ->pluck('result_id');
        $followers = Follower::where('my_id', $client->id)
            ->select('id_follower')->pluck('id_follower');

        $post = Post::join('clients', 'clients.id', '=', 'posts.id_client')
            ->select('posts.*', 'clients.username', 'clients.fullname', 'clients.avatar')
            ->where(function ($query) use ($friends, $id_client) {
                $query->where('posts.privacy', Post::friend)
                    ->whereIn('posts.id_client', $friends)
                    ->orWhere('posts.id_client', $id_client);
            })
            ->orWhere(function ($query) use ($id_client, $friends, $followers) {
                $query->where('posts.privacy', Post::public);
                    // ->where(function ($query) use ($id_client, $friends, $followers) {
                    //     $query->whereIn('posts.id_client', $friends)
                    //         ->orWhereIn('posts.id_client', $followers)
                    //         ->orWhere('posts.id_client', $id_client);
                    // });
            })
            ->orWhere(function ($query) use ($id_client) {
                $query->where('posts.privacy', Post::private)
                    ->where('posts.id_client', $id_client);
            })
            ->orderByDESC('posts.created_at')
            ->get();
        foreach ($post as $key => $value) {
            $check = PostLike::where('id_post', $value->id)->where('id_client', $client->id)->first();
            $totalLikes = PostLike::where('id_post', $value->id)->count();
            if ($check) {
                $post[$key]['liked'] = 1;
            }else{
                $post[$key]['liked'] = 0;
            }
            $post[$key]['likes'] = $totalLikes;
        }
        return response()->json([
            'status' => 1,
            'dataPost'    => $post,
            'message'    => 'oke',
        ]);
    }
    public function getAllPosts(Request $request){
        $posts = Post::join('clients', 'clients.id', '=', 'posts.id_client')
            ->leftJoin('post_likes', 'post_likes.id_post', '=', 'posts.id')
            ->leftJoin('comments', 'comments.id_post', '=', 'posts.id')
            ->select('posts.id', 'clients.fullname', 'clients.avatar', 'posts.images', 'posts.created_at', 'posts.caption', 
                DB::raw('COUNT(DISTINCT post_likes.id) as react'),
                DB::raw('COUNT(DISTINCT comments.id) as comment'),
                'posts.privacy')
            ->groupBy('posts.id', 'clients.fullname', 'clients.avatar', 'posts.images', 'posts.created_at', 'posts.caption', 'posts.privacy')
            ->orderByDesc('posts.created_at')
            ->get()
            ->toArray();
        // Parse the images field
        foreach ($posts as &$post) {
            $post['images'] = json_decode($post['images']);
        }
        return response()->json([
            'status' => 1,
            'data' => $posts,
            'message' => 'oke',
        ]);
    }
    public function deletePost(Request $request){
        $post = Post::find($request->id);
        if ($post) {
            $post->update(['privacy' => 2]);
            return response()->json([
                'status' => 1,
                'message' => 'Post privacy updated successfully!',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Update error!',
            ]);
        }
    }
}