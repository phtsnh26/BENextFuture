<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    //
    public function show($image)
    {
        if($image)
        $path = public_path('/img/' . $image);

        if (file_exists($path)) {
            return response()->file($path);
        } else {
            abort(404);
        }
    }
}
