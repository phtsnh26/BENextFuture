<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Follower;
use App\Models\Friend;
use App\Models\LinkAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function data($username){
        $myData = Client::where('username', $username)->first();
        return response()->json([
            'myData'   => $myData,
        ]);
    }
    public function getAboutMe($username)
    {
        $info = Client::where('username', $username)->first();
        $data = Client::find($info->id);
        $link_address = LinkAddress::leftJoin('clients', 'link_addresses.id_client', 'clients.id')
            ->where('id_client', $info->id)
            ->get();
        return response()->json([
            'data' => $data,
            'link_address' => $link_address
        ]);
    }
    public function dataAll(Request $request, $username)
    {
        $info = Client::where('username', $username)->first();
        $a = $request->user()->id;
        $b = $info->id;

        $friends = DB::table(DB::raw("(SELECT id_friend as result_id FROM friends WHERE my_id = $b
                UNION
                SELECT my_id as result_id FROM friends WHERE id_friend = $b) as list_friend"))
            ->leftJoin('clients', 'clients.id', '=', 'list_friend.result_id')
            ->select(
                'clients.username',
                'clients.fullname',
                'clients.avatar',
                'clients.id',
                'clients.nickname',
                DB::raw("CASE
                    WHEN clients.id IN (
                        SELECT id_friend as result_id FROM friends WHERE my_id = $a
                        UNION
                        SELECT my_id as result_id FROM friends WHERE id_friend = $a
                    ) THEN 'Unfriend'
                    WHEN clients.id IN (
                        SELECT my_id FROM followers WHERE id_follower = $a AND status = 0
                    ) THEN 'Confirm'
                    WHEN clients.id IN (
                        SELECT id_follower FROM followers WHERE my_id = $a AND status = 0
                    ) THEN 'Tancel'
                    WHEN clients.id = $a THEN 'Z'
                    ELSE 'Add friend'
                END AS status")
            )
            ->orderByDesc('status')
            ->get();
        $follower = DB::table('followers')
            ->leftJoin('clients', 'followers.my_id', '=', 'clients.id')
            ->select(
                'clients.username',
                'clients.fullname',
                'clients.nickname',
                'clients.avatar',
                'clients.id',
                DB::raw("
                    CASE
                        WHEN clients.id IN (
                            SELECT id_friend as result_id FROM friends WHERE my_id = $a
                            UNION
                            SELECT my_id as result_id FROM friends WHERE id_friend = $a
                        ) THEN 'Unfriend'
                        WHEN clients.id IN (
                            SELECT my_id FROM followers WHERE id_follower = $a AND status = 0
                        ) THEN 'Confirm'
                        WHEN clients.id IN (
                            SELECT id_follower FROM followers WHERE my_id = $a AND status = 0
                        ) THEN 'Tancel'
                        WHEN clients.id = $a Then 'Z'
                        ELSE 'Add friend'
                    END AS status
                ")
            )
            ->where('id_follower', $b)
            ->orderByDesc('status')
            ->get();

        return response()->json([
            'friends' => $friends,
            'followers' => $follower
        ]);
    }
    public function dataAccount(Request $request)
    {
        $myInfo = $request->user()->id;
        $data = Client::where('id', $myInfo)
            ->first();
        return response()->json([
            'data'    => $data
        ]);
    }
    public function updateProfile(Request $request)
    {
        $avatar = $request->file('avatar');
        if ($avatar) {
            $path =  uniqid() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = 'img/avatar/' . $path;
            $avatar->move(public_path('img/avatar'), $avatarPath);
            $avatarUrl = 'avatar/' . $path;
        } else {
            $avatarUrl = null;
        }
        $myInfo = $request->user()->id;
        $data = Client::find($myInfo);

        if ($data) {
            if ($request->gender != $data->gender && in_array($avatarUrl ? $avatarUrl : $data->avatar, ['avatar_male.jpg', 'avatar_female.jpg', 'avatar_other.jpg'])) {
                if ($request->gender == Client::male) {
                    $avatarUrl = 'avatar_male.jpg';
                } else if ($request->gender == Client::female) {
                    $avatarUrl = 'avatar_female.jpg';
                } else {
                    $avatarUrl = 'avatar_other.jpg';
                }
            }

            $data->update([
                'phone_number' => $request->phone_number ?? $data->phone_number,
                'fullname' => $request->fullname ?? $data->fullname,
                'date_of_birth' => $request->date_of_birth ?? $data->date_of_birth,
                'avatar' => $avatarUrl ?? $data->avatar,
                'gender' => $request->gender ?? $data->gender,
                'nickname' => $request->nickname ?? $data->nickname,
                'address' => $request->address ?? $data->address,
                'bio' => $request->bio ?? $data->bio,
            ]);
        }
        return response()->json([
            'status' => 1,
            'message' => 'Update profile successfully'
        ]);
    }
    public function dataLinkAddress($username)
    {
        $myInfo = Client::where('username', $username)->first();
        $link = LinkAddress::where('id_client', $myInfo->id)
            ->get();
        return response()->json([
            'data'    => $link
        ]);
    }
    public function dataLinkAddressProfile(Request $request)
    {
        $myInfo = $request->user();
        $link = LinkAddress::where('id_client', $myInfo->id)
            ->get();
        return response()->json([
            'data'    => $link
        ]);
    }
    public function updateLink(Request $request)
    {
        $myInfo = $request->user();
        $links = LinkAddress::where('id_client', $myInfo->id)->get();
        $data = $request->all();
        foreach ($data as $key => $value) {
            if ($key != 'status' && (isset($value['link']))) {
                $check = -1;
                foreach ($links as $k => $v) {
                    if ($v['type'] == $value['type']) {
                        $check = $v['id'];
                        break;
                    }
                }
                if ($check != -1) {
                    LinkAddress::find($check)->update($value);
                } else {
                    LinkAddress::create([
                        'id_client'     => $myInfo->id,
                        'link'          => $value['link'],
                        'type'          => $value['type'],
                        'icon'          => LinkAddress::checkType($value['type']),
                        'name'          => $value['name'],
                    ]);
                }
            } else if ($key != 'status' && (!isset($value['link']))) {
                LinkAddress::where('id_client', $myInfo->id)->where('type', $value['type'])->delete();
            }
        }
        return response()->json([
            'status'    => 1,
            'message'   => 'Updated address link successfully',
        ]);
    }
}
