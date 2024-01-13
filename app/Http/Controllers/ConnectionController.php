<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Connection;
use App\Models\RequestGroup;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConnectionController extends Controller
{
    public function approveConnection(Request $request)
    {
        try {
            DB::beginTransaction();
            Connection::create([
                'id_client' => $request->id,
                'id_group' => $request->id_group,
                'id_role' => Role::member,
            ]);
            RequestGroup::where('id_group', $request->id_group)
                ->where('id_invite', $request->id)
                ->delete();
            DB::commit();
            return response()->json([
                'status'    => 1,
                'message'   => 'Approved successfully'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'    => 0,
                'message'   => $th
            ]);
        }
    }
    public function approveConnectionAll(Request $request)
    {
        try {
            DB::beginTransaction();
            $approve = RequestGroup::where('id_group', $request->id_group)
                ->get();
            foreach ($approve as $key => $value) {
                Connection::create([
                    'id_client' => $value['id_invite'],
                    'id_group' => $value['id_group'],
                    'id_role' => Role::member,
                ]);
                $value->delete();
            }
            DB::commit();
            return response()->json([
                'status'    => 1,
                'message'   => 'Approved all successfully',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'    => 0,
                'message'   => $th
            ]);
        }
    }
    public function refuseConnection(Request $request)
    {
        try {
            DB::beginTransaction();
            $refuse = RequestGroup::where('id_group', $request->id_group)
                ->where('id_invite', $request->id)
                ->first();
            if ($refuse) {
                $refuse->delete();
            }
            DB::commit();
            return response()->json([
                'status'    => 1,
                'message'   => 'Refuse successfully',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'    => 0,
                'message'   => $th
            ]);
        }
    }
    public function refuseConnectionAll(Request $request)
    {
        try {
            DB::beginTransaction();
            $refuse = RequestGroup::where('id_group', $request->id_group)
                ->get();
            foreach ($refuse as $key => $value) {
                $value->delete();
            }
            DB::commit();
            return response()->json([
                'status'    => 1,
                'message'   => 'Refuse all successfully',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'    => 0,
                'message'   => $th
            ]);
        }
    }
    public function checkRole(Request $request)
    {
        $check = Connection::where('id_client', $request->user()->id)->where('id_group', $request->id_group)->first();
        if ($check) {
            if (count(array_intersect([$check->id_role], [Role::member, Role::post_moderator]))) {
                return response()->json(['viewType' => 2]);
            } else {
                return response()->json(['viewType' => 1]);
            }
        } else {
            return response()->json([
                'viewType'    => 0,
            ]);
        }
    }
    public function checkRequest(Request $request)
    {

        $check = RequestGroup::where('id_group', $request->id_group)->where('id_invite', $request->user()->id)
            ->first();
        if ($check && $check->status == RequestGroup::come) {
            return response()->json([
                'check'    => 1, 
            ]);
        } else {
            if (!$check) {
                return response()->json(['check' => 0]);
            }
            $client = RequestGroup::leftJoin('clients', 'clients.id', 'request_groups.id_client')
                ->select('request_groups.updated_at as time', 'clients.*')
                ->where('id_client', $check->id_client)
                ->where('id_invite', $check->id_invite)
                ->get();
            return response()->json([
                'check' => -1,
                'client' => $client
            ]);
        }
    }
    public function undoRequest(Request $request)
    {
        $client = $request->user();
        $check = RequestGroup::where('id_group', $request->id_group)->where('id_invite', $client->id)->first();
        if ($check) {
            $check->delete();
            return response()->json([
                'status'    => 1,
                'message'   => 'Undo invitation successfully!',
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'The group does not exist or there is a login error!',
            ]);
        }
    }
    public function leaveGroup(Request $request){
        $client = $request->user();
        try {
            DB::beginTransaction();
            $del = Connection::where('id_group', $request->id)->where('id_client', $client->id)->first();
            if($del->id_role == Role::admin){
                return response()->json([
                    'status'    => -1,
                    'message'   => 'Please assign admin rights to someone before you leave the group!',
                ]);
            }else{
                $del->delete();
            }
            DB::commit();
            return response()->json([
                'status'    => 1,
                'message'   => 'Leave group successfully!' . $del->status,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'    => 0,
                'message'   => 'Leave group failed!',
            ]);
        }
    }
}
