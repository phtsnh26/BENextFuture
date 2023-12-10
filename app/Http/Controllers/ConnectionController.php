<?php

namespace App\Http\Controllers;

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
                'id_role' => Role::newbie,
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
                    'id_role' => Role::newbie,
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
}
