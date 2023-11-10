<?php

namespace App\Http\Controllers;

use App\Models\Client;
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
        if($request->gender == 0){
            $avata = "avata_female.jpg";
        } else if ($request->gender == 1) {
            $avata = "avata_male.jpg";
        }else{
            $avata = "other.jpg";
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

    public function getProfile(Request $request){
        return response()->json([
            'myData'    => $request->user(),
        ]);
    }


}
