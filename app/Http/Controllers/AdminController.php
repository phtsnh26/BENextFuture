<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
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
    public function getAllAccounts(Request $request){
        $accounts = Client::all()->toArray();
        return response()->json($accounts);
    }
    public function banAccount(Request $request){
        $user = Client::find($request->id);
        if ($user) {
            $user->update(['status' => 0]);
            return response()->json([
                'status' => 1,
                'message' => 'Account banned successfully!',
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Ban error: Account not found!',
            ]);
        }
    }
}