<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\Friend;
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


        $dataStory = Stories::leftJoin('clients', 'clients.id', 'stories.id_client')
            ->select('stories.*', 'clients.fullname', 'clients.avatar')
            ->where(function ($query) use ($id_client, $friends, $followers, $now) {
                $query->where('stories.created_at', '>=', $now->subDay())
                    ->where(function ($query) use ($id_client, $friends, $followers) {
                        $query->where('stories.privacy', Stories::public)
                            ->where(function ($query) use ($id_client, $friends, $followers) {
                                $query->where('stories.id_client', $id_client)
                                    ->orWhereIn('stories.id_client', $friends)
                                    ->orWhereIn('stories.id_client', $followers);
                            })
                            ->orWhere(function ($query) use ($id_client, $friends) {
                                $query->where('stories.privacy', Stories::friend)
                                    ->where(function ($query) use ($id_client, $friends) {
                                        $query->where('stories.id_client', $id_client)
                                            ->orWhereIn('stories.id_client', $friends);
                                    });
                            })
                            ->orWhere(function ($query) use ($id_client) {
                                $query->where('stories.privacy', Stories::private)
                                    ->where('stories.id_client', $id_client);
                            });
                    });
            })
            ->limit(4)
            ->orderBy('stories.created_at', 'desc')
            ->paginate(4, ['*'], 3);

        return response()->json([
            'dataStory'    => $dataStory,
        ]);
    }
    public function getAllStory(Request $request)
    {
        $now = Carbon::now();
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
        $allStory = Stories::leftJoin('clients', 'clients.id', 'stories.id_client')
            ->select('stories.*', 'clients.fullname', 'clients.avatar')
            ->where(function ($query) use ($id_client, $friends, $followers, $now) {
                $query->where('stories.created_at', '>=', $now->subDay())
                    ->where(function ($query) use ($id_client, $friends, $followers) {
                        $query->where('stories.privacy', Stories::public)
                            ->where(function ($query) use ($id_client, $friends, $followers) {
                                $query->where('stories.id_client', $id_client)
                                    ->orWhereIn('stories.id_client', $friends)
                                    ->orWhereIn('stories.id_client', $followers);
                            })
                            ->orWhere(function ($query) use ($id_client, $friends) {
                                $query->where('stories.privacy', Stories::friend)
                                    ->where(function ($query) use ($id_client, $friends) {
                                        $query->where('stories.id_client', $id_client)
                                            ->orWhereIn('stories.id_client', $friends);
                                    });
                            })
                            ->orWhere(function ($query) use ($id_client) {
                                $query->where('stories.privacy', Stories::private)
                                    ->where('stories.id_client', $id_client);
                            });
                    });
            })
            ->limit(4)
            ->orderBy('stories.created_at', 'desc')
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
        $imageName = 'stories/' . time() . '.png';
        File::put(public_path('img/' . $imageName), $imageData);
        $check = Stories::create([
            'image'             => $imageName,
            'privacy'            => $request->privacy,
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


    public function detailStory(Request $request, $id)
    {
        $data = Stories::find($id);
        return response()->json([
            'data'    => $data,
        ]);
    }
}
