<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                    'status'    => -1,
                    'message'   => 'Your account has been locked !',
                ]);
            } else {
                return response()->json([
                    'status'    => 1,
                    'message'   => 'Login successfully !',
                ]);
            }
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Wrong account or password !',
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
