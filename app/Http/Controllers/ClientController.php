<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Follower;
use App\Models\Friend;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $friend = Friend::where('my_id', $client->id)
            ->pluck('id_friend');
        $data = Client::where('id', '!=', $client->id)
            ->whereNotIn('id', $friend->toArray())
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
            'myData'    => $request->user(),
        ]);
    }
}
