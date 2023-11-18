<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
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
                    $fileNames[] = time() . "_" . $file_name;
                }
            }
            $request->merge(['img' => $fileNames]);
            $result = implode(',', $request->img);
            $arr = $request->except('images'); // Loại bỏ key 'images' nếu nó tồn tại trong request
            $arr['images'] = $result; // Thêm key 'images' với giá trị từ $result
        } else {
            $arr = $request->all(); // Loại bỏ key 'images' nếu nó tồn tại trong request
        }
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
        $post = Post::join('clients', 'clients.id', 'posts.id_client')
            ->select('posts.*', 'clients.username', 'clients.fullname', 'clients.avatar')
            ->orderBy('posts.created_at', 'desc')
            ->get();
        return response()->json([
            'status' => 1,
            'dataPost'    => $post,
            'message'    => 'oke',
        ]);
    }
}
