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
    public function resentMail(Request $request)
    {

        $user = Client::where('email', $request->email)
            ->first();
        if ($user) {
            $user->update([
                'hash_active' => mt_rand(100000, 999999)
            ]);
            $dataMail['code'] = $user->hash_active;
            $dataMail['fullname'] = $user->fullname;
            Mail::to($request->email)->send(new ActiveMail($dataMail));
            return response()->json([
                'status'    => 1,
                'message'   => 'Sent mail successfully',
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Email does not exist',
            ]);
        }
    }
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
                if ($user_hash_active) {
                    $user_hash_active->update([
                        'hash_active' => null
                    ]);
                }
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
        $user = Client::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();
        if ($user && Hash::check($request->password, $user->password)) {
            if ($user->status == Client::banned_account) {
                return response()->json([
                    'status'    => -2,
                    'message'   => 'Your account has been banned',
                ]);
            } else {
                if ($user->is_active == 1) {
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
                } else {
                    if ($user->hash_active == null) {
                        $user->hash_active = mt_rand(100000, 999999);
                        $user->save();
                        $dataMail['code'] = $user->hash_active;
                        $dataMail['fullname'] = $user->fullname;
                        Mail::to($user->email)->send(new ActiveMail($dataMail));
                    }
                    return response()->json([
                        'status' => -1,
                        'email' => $user->email,
                        'message' => 'Your account has not been activated',
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Invalid login information',
            ]);
        }
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
