<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ImageController extends Controller
{
    //
    public function show($image)
    {
        if ($image)
            $path = public_path('/img/' . $image);

        if (file_exists($path)) {
            return response()->file($path);
        } else {
            abort(404);
        }
    }
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $file->move(public_path('img'), $fileName);

            return response()->json(['message' => 'Tệp đã được tải lên thành công']);
        } else {
            return response()->json(['error' => 'Không có tệp nào được tải lên.'], 400);
        }
    }

    public function uploadImage(Request $request)
    {
        $imageData = $request->input('image');
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));
        $imageName = time() . '.png';
        File::put(public_path('img/' . $imageName), $imageData);

        return response()->json([
            'message' => 'Ảnh đã được tải lên và lưu trữ.',
            'status' => 1,
        ]);

    }
}
