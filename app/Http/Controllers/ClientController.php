<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function signUp(Request $request)
    {
        $sign_up = Client::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'fullname' => $request->fullname,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
        ]);
        if ($sign_up) {
            return response()->json([
                'status'    => 1,
                'message'   => 'Sign Up Successfully !',
            ]);
        }
    }

    public function signIn(Request $request)
    {
        $check_1 = Auth::guard('client')->attempt(['email' => $request->username, 'password' => $request->password]);
        $check_2 = Auth::guard('client')->attempt(['username' => $request->username, 'password' => $request->password]);
        if ($check_1 || $check_2) {
            $client   =   Auth::guard('client')->user();
            if ($client->status == Client::lock_account) {
                Auth::guard('client')->logout();
                return response()->json([
                    'status'    => 0,
                    'message'   => 'Your account has been locked !',
                ]);
            } else {
                return response()->json([
                    'status'    => 1,
                    'message'   => 'Login successfully !',
                    'client'    => $client
                ]);
            }
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Wrong account or password !',
            ]);
        }
    }

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
        $user = Client::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'fullname' => $request->fullname,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
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
    


}
