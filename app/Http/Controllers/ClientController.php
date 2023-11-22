<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Follower;
use App\Models\Friend;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{

    public function login(Request $request)
    {
        $user = Client::where("email", $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {

            if (!isset($request->remember) || $request->remember == false) {
                Auth::guard('client')->login($user);
            } else {
                Auth::guard('client')->login($user, true);
            }
            $authenticatedUser = Auth::guard('client')->user();
            $tokens = $authenticatedUser->tokens;
            $limit = 4;
            if ($tokens->count() >= $limit) {
                // Giữ lại giới hạn số lượng token
                $tokens->sortByDesc('created_at')->slice($limit)->each(function ($token) {
                    $token->delete();
                });
            }
            $token = $authenticatedUser->createToken('authToken', ['*'], now()->addDays(7));

            return response()->json([
                'status' => 1,
                'token' => $token->plainTextToken,
                'type_token' => 'Bearer',
            ]);
        }
        return response()->json([
            'status' => 0,
            'message' => 'Invalid login information',
        ]);
    }


    public function register(Request $request)
    {
        if ($request->gender == 0) {
            $avata = "avatar_female.jpg";
        } else if ($request->gender == 1) {
            $avata = "avatar_male.jpg";
        } else {
            $avata = "avatar_other.jpg";
        }
        $user = Client::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'fullname' => $request->fullname,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'avatar' => $avata,
        ]);
        if ($user) {
            return response()->json([
                'status'    => 1,
                'message'    => "Your account has been successfully created!",
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'    => "Fail",
            ]);
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

        $friendIds = $friends->pluck('result_id')->toArray();
        $data = Client::where('id', '!=', $client->id)
            ->whereNotIn('id', $friendIds)
            ->whereNotIn('id', $follower->toArray())
            ->whereNotIn('id', $follow->toArray())
            ->inRandomOrder()
            ->get();
        return response()->json([
            'data' => $data,
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
            if ($friend->contains($info->id)) {
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
}
