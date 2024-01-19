<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpRequest;
use App\Mail\ActiveMail;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AccountController extends Controller
{
    public function deleteActive(Request $request)
    {
        Client::where('email', $request->email)->update([
            'hash_active' => null
        ]);
        return response()->json([
            'status'    => 1,
            'message'   => 'Deleted hash active successfully',
        ]);
    }
    public function activeMail(Request $request)
    {
        $hash_active = $request->hash_active;
        $user_hash_active = Client::where('hash_active', $hash_active)->first();

        if ($user_hash_active) {
            if ($user_hash_active->is_active == 0) {
                $user_hash_active->is_active = 1;
                $user_hash_active->save();

                return response()->json([
                    'status'  => 1,
                    'message' => "Your account has been successfully activated",
                ]);
            } else {
                return response()->json([
                    'status'  => 0,
                    'message' => "Account is already activated",
                ]);
            }
        } else {
            return response()->json([
                'status'  => 0,
                'message' => "Invalid confirmation code. Please try again",
            ]);
        }
    }
    public function register(SignUpRequest $request)
    {
        if ($request->gender == 0) {
            $avata = "avatar_female.jpg";
        } else if ($request->gender == 1) {
            $avata = "avatar_male.jpg";
        } else {
            $avata = "avatar_other.jpg";
        }
        $randomSixDigits = mt_rand(100000, 999999);

        $user = Client::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'fullname' => $request->fullname,
            'nickname' => $request->username,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'avatar' => $avata,
            'hash_active' => $randomSixDigits,
        ]);
        $dataMail['code']          =   $randomSixDigits;
        $dataMail['fullname']      =   $request->fullname;
        Mail::to($request->email)->send(new ActiveMail($dataMail));
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

    public function login(Request $request)
    {
        $user = Client::where("email", $request->username)->orWhere('username', $request->username)->first();
        if ($user->is_active == 1) {
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
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Your account has not been activated',
            ]);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Invalid login information',
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
