<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\PostGroup;
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
            $arr['privacy'] = $request->privacy;
            $arr['id_client'] = $client->id;
            $group = Group::find($arr['id_group']);
            if ($group->post_approval == Group::requiredPostInGroupApproval) {
                $arr['status'] = 0;
            } else {
                $arr['status'] = 1;
            }
            $post = PostGroup::create($arr);

            DB::commit();

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
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'    => 0,
                'message'   => 'Fail rồi con gà',
            ]);
        }
    }
}
