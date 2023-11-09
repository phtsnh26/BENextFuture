<?php

namespace App\Http\Controllers;

use App\Models\Stories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class StoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getStory(Request $request)
    {
        $client = $request->user();
        return $client;
    }
    public function store(Request $request)
    {
        $client = $request->user();
        $imageData = $request->input('image');
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));
        $imageName = time() . '.png';
        File::put(public_path('img/' . $imageName), $imageData);
        $check = Stories::create([
            'image'             => $imageName,
            'status'            => $request->status,
            'id_client'         => $client->id,
        ]);
        if ($check) {
            return response()->json([
                'message' => 'Upload Story successfully!',
                'status' => 1,
            ]);
        } else {
            return response()->json([
                'message' => 'có lỗi xảy ra.',
                'status' => 0,
            ]);
        }
    }

}
