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
        $result = Stories::where('created_at', '>=', $now->subDays(15))
            ->groupBy('id_client')
            ->select('id_client')
            ->limit(4)
            ->get();
        foreach ($result as $key => $value) {
            $dataStoryOfClient = Stories::leftJoin('clients', 'clients.id', 'stories.id_client')
                ->select('stories.*', 'clients.fullname', 'clients.avatar')
                ->where(function ($query) use ($id_client, $friends, $now) {
                    $query->where('stories.created_at', '>=', $now->subDays(15))
                        ->where(function ($query) use ($id_client, $friends) {
                            $query->where(function ($query) {
                                $query->where('stories.privacy', Stories::public);
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
                ->where('id_client', $value['id_client'])
                ->orderBy('stories.created_at', 'desc')
                ->get();
            $result[$key]['dataStory'] = $dataStoryOfClient;
        }
        return response()->json([
            'dataStory'    => $result,
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

        $result = Stories::where('created_at', '>=', $now->subDays(15))
            ->groupBy('id_client')
            ->select('id_client')
            ->get();
        foreach ($result as $key => $value) {
            $allStoryOfClient = Stories::leftJoin('clients', 'clients.id', 'stories.id_client')
                ->select('stories.*', 'clients.fullname', 'clients.avatar')
                ->where(function ($query) use ($id_client, $friends, $followers, $now) {
                    $query->where('stories.created_at', '>=', $now->subDays(15))
                        ->where(function ($query) use ($id_client, $friends, $followers) {
                            $query->where(function ($query) {
                                $query->where('stories.privacy', Stories::public);
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
                ->orderBy('stories.created_at')
                ->where('id_client', $value['id_client'])
                ->get();
            $result[$key]['dataStory'] = $allStoryOfClient;
        }
        return response()->json([
            'allStory'   => $result,
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
