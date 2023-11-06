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
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $imageData = $request->input('image');
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));
        $imageName = time() . '.png';
        File::put(public_path('img/' . $imageName), $imageData);
        $check = Stories::create([
            'image'             => $imageName,
            'status'            => $request->status,
            'id_client'         => 1,
        ]);
        if ($check) {
            return response()->json([
                'message' => 'Upload Story successfully!',
                'status' => 1,
            ]);
        }else{
            return response()->json([
                'message' => 'có lỗi xảy ra.',
                'status' => 0,
            ]);

        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Stories $stories)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stories $stories)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stories $stories)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stories $stories)
    {
        //
    }
}
