<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpRequest;
use App\Models\Client;
use App\Models\Connection;
use App\Models\Follower;
use App\Models\Friend;
use App\Models\Group;
use App\Models\PostGroup;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{

    public function signOut(Request $request)
    {
        $client = $request->user();

        if ($client) {
            $client->currentAccessToken()->delete();
            return response()->json(['status' => 1]);
        } else {
            return response()->json(['status' => 0]);
        }
    }

    public function getData()
    {
        $data = Client::all();
        return response()->json([
            'data' => $data,
        ]);
    }

    public function getAllData(Request $request)
    {
        $client = $request->user();
        $follow = Follower::join('clients', 'clients.id', 'followers.my_id')
            ->where('id_follower', $client->id)
            ->Where('followers.status', Follower::friend_request)
            ->select('clients.*')
            ->pluck('clients.id');
        $follower = Follower::where('my_id', $client->id)
            ->where('status', '!=', Follower::un_friend_request)
            ->pluck('id_follower');

        $userId = $client->id;
        $friends = DB::table('friends')
            ->select('id_friend as result_id')
            ->where('my_id', $userId)
            ->union(
                DB::table('friends')
                    ->select('my_id as result_id')
                    ->where('id_friend', $userId)
            )
            ->get();
        $birthDate = new DateTime($client->date_of_birth);
        $birthYear = $birthDate->format('Y');

        $friendIds = $friends->pluck('result_id')->toArray();
        $data = Client::where('id', '!=', $client->id)
            ->whereNotIn('id', $friendIds)
            ->whereNotIn('id', $follower->toArray())
            ->whereNotIn('id', $follow->toArray());
        if ($birthYear >= 2000) {
            $data->orderByDESC('date_of_birth');
        } else {
            $data->orderBy('date_of_birth');
        }
        $result = $data->get();

        foreach ($result as $key => $value) {
            $mutual = array_intersect(Client::getFriend($value['id']), Client::getFriend($client->id));
            $result[$key]->mutual = count($mutual);
        }
        return response()->json([
            'data' => $result,
        ]);
    }
    public function getProfile(Request $request)
    {
        return response()->json([
            'myInfo'    => $request->user(),
        ]);
    }
    public function getInfo(Request $request, $username)
    {
        $client =   $request->user();
        $info = Client::where('username', $username)->first();

        // select id_follower
        // from followers
        // where my_id = 1
        $request_friend  = Follower::where('my_id', $client->id)         // mình gửi kết bạn cho họ mà họ chưa chấp nhận
            ->where('status', Follower::friend_request)
            ->select('id_follower')
            ->pluck('id_follower');

        // select my_id
        // from followers
        // where id_follower = 1
        $follower = Follower::where('id_follower', $client->id)          // họ gửi kết bạn cho mình mà mình chưa chấp nhận
            ->where('status', Follower::friend_request)
            ->select('my_id')
            ->pluck('my_id');


        // select id_friend as result_id
        // from friends
        // where my_id = 5
        // UNION
        // SELECT my_id as result_id
        // from friends
        // where id_friend = 5
        $friend = Friend::select('id_friend as result_id')              // danh sách bạn bè của người đang đăng nhập
            ->where('my_id', $client->id)
            ->union(
                Friend::select('my_id as result_id')
                    ->where('id_friend', $client->id)
            )
            ->pluck('result_id');

        if ($info) {
            if ($info->id === $client->id) {
                return response()->json([
                    'status' => 'self',
                    'info' => $info,
                ]);
            } else if ($friend->contains($info->id)) {
                return response()->json([
                    'status'    => 'friend',
                    'info'   => $info,
                ]);
            } else {
                if ($request_friend->contains($info->id)) {
                    return response()->json([
                        'status'    => 'request_friend',
                        'info'   => $info,
                    ]);
                } else {
                    if ($follower->contains($info->id)) {
                        return response()->json([
                            'status'    => 'follower',
                            'info'   => $info,
                        ]);
                    } else {
                        return response()->json([
                            'status'    => 'stranger',
                            'info'   => $info,
                        ]);
                    }
                }
            }
        }
    }

    public function search(Request $request)
    {
        $client = $request->user();
        $keySearch = '%' . $request->keySearch . '%';
        $data = Client::where(function ($query) use ($keySearch) {
            $query->where('username', 'like', $keySearch)
                ->orWhere('fullname', 'like', $keySearch)
                ->orWhere('nickname', 'like', $keySearch);
        })
            ->where('id', '!=', $client->id)
            ->limit(5)
            ->get();
        return response()->json([
            'dataSearch' => $data
        ]);
    }
    public function authorization(Request $request): \Illuminate\Http\JsonResponse
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['message' => 'Token is missing'], 401);
        }
        if (Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Token is valid',
                'status' => true,
            ], 200);
        }

        return response()->json([
            'message' => 'Token is invalid',
            'status' => false
        ], 200);
    }
}
