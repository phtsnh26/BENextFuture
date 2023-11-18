<?php

namespace App\Http\Controllers;

use App\Models\Stories;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class StoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getStory(Request $request)
    {
        $now = Carbon::now();
        $client = $request->user();
        $dataStory = Stories::leftjoin('clients', 'clients.id', 'stories.id_client')
            ->where('stories.created_at', '>=', $now->subDay()) // Lọc các stories trong 24 tiếng trở lại đây
            ->select('stories.*', 'clients.fullname', 'clients.avatar')
            ->limit(4)
            ->orderBy('created_at', 'desc')
            ->paginate(4, ['*'], 3);
        return response()->json([
            'dataStory'    => $dataStory,
        ]);
    }
    public function getAllStory(Request $request)
    {
        $now = Carbon::now();
        $allStory = Stories::leftjoin('clients', 'clients.id', 'stories.id_client')
        ->select('stories.*', 'clients.fullname', 'clients.avatar')
        ->where('stories.created_at', '>=', $now->subDay()) // Lọc các stories trong 24 tiếng trở lại đây
        ->orderBy('created_at', 'desc')
        ->get();
        return response()->json([
            'allStory'   => $allStory,
        ]);
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


    public function detailStory(Request $request, $id){
        $data = Stories::find($id);
        return response()->json([
            'data'    => $data,
        ]);
    }
}
