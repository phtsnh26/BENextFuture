<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\Friend;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    public function removeMember(Request $request)
    {
        try {
            DB::beginTransaction();
            $client =   $request->user();
            $me = Connection::where('id_group', $request->id_group)
                ->where('id_client', $client->id)
                ->first();
            $remove = Connection::where('id_client', $request->id)
                ->where('id_group', $request->id_group)
                ->first();
            if ($me->id_role == Role::admin && $remove->id_role != Role::admin) {
                $remove->delete();
            } else if ($me->id_role == Role::member_moderator && $remove->id_role != Role::member_moderator && $remove->id_role != Role::admin && $remove->id_role != Role::moderator) {
                $remove->delete();
            } else if ($me->id_role == Role::moderator && $remove->id_role != Role::moderator && $remove->id_role != Role::admin) {
                $remove->delete();
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => 'You do not have permission to delete this person',
                ]);
            }
            DB::commit();
            return response()->json([
                'status'    => 1,
                'message'   => "Remove member successfully"
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'    => 0,
                'message'   => $th,
            ]);
        }
    }
    public function grantPermissions(Request $request)
    {
        try {
            DB::beginTransaction();
            $client = $request->user();
            $grant_permission = Connection::where('id_client', $client->id)
                ->where('id_group', $request->id_group)
                ->first();
            $other = Connection::where('id_client', $request->id)
                ->where('id_group', $request->id_group)
                ->first();
            if ($grant_permission->id_role == Role::moderator && $other->id_role != Role::moderator && $other->id_role != Role::admin) {
                if ($other->id_role == Role::post_moderator || $other->id_role == Role::member_moderator) {
                    $other->update([
                        'id_role' => Role::moderator,
                    ]);
                } else {
                    $other->update([
                        'id_role' => $request->id_role,
                    ]);
                }
            } else if ($grant_permission->id_role == Role::admin && $other->id_role != Role::admin) {
                if ($other->id_role == Role::post_moderator || $other->id_role == Role::member_moderator) {
                    $other->update([
                        'id_role' => Role::moderator,
                    ]);
                } else {
                    $other->update([
                        'id_role' => $request->id_role,
                    ]);
                }
            } else if ($grant_permission->id_roe == Role::member_moderator && $other->id_role == Role::member) {
                $other->update([
                    'id_role' => $request->id_role,
                ]);
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => 'You do not have enough rights to grant permissions to this person',
                ]);
            }
            DB::commit();
            return response()->json([
                'status'    => 1,
                'message'   => "Grant permissions successfully",
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'    => 0,
                'message'   => $th,
            ]);
        }
    }
    public function removePermissions(Request $request)
    {
        try {
            DB::beginTransaction();
            $client = $request->user();
            $grant_permission = Connection::where('id_client', $client->id)
                ->where('id_group', $request->id_group)
                ->first();
            $other = Connection::where('id_client', $request->id)
                ->where('id_group', $request->id_group)
                ->first();
            if ($grant_permission->id_role == Role::moderator && $other->id_role != Role::moderator && $other->id_role != Role::admin) {
                if ($other->id_role == Role::post_moderator || $other->id_role == Role::member_moderator) {
                    $other->update([
                        'id_role' => Role::member,
                    ]);
                }
            } else if ($grant_permission->id_role == Role::admin && $other->id_role != Role::admin) {
                if ($other->id_role == Role::post_moderator || $other->id_role == Role::member_moderator) {
                    $other->update([
                        'id_role' => Role::member,
                    ]);
                } else if ($other->id_role == Role::moderator) {
                    if ($request->id_role == Role::post_moderator) {
                        $other->update([
                            'id_role' => Role::member_moderator,
                        ]);
                    } else if ($request->id_role == Role::member_moderator) {
                        $other->update([
                            'id_role' => Role::post_moderator,
                        ]);
                    } else {
                        $other->update([
                            'id_role' => Role::member,
                        ]);
                    }
                }
            } else if ($grant_permission->id_role == Role::member_moderator && $other->id_role == Role::post_moderator) {
                $other->update([
                    'id_role' => Role::member,
                ]);
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => 'You do not have enough rights to removed permissions to this person',
                ]);
            }
            DB::commit();
            return response()->json([
                'status'    => 1,
                'message'   => "Removed permissions successfully",
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'    => 0,
                'message'   => $th,
            ]);
        }
    }
    public function searchMember(Request $request)
    {
        $keyword = '%' . $request->keyword . '%';
        $client = $request->user();
        $friends = Friend::where('id_friend', $client->id)
            ->select('my_id')
            ->Union(
                Friend::where('my_id', $client->id)
                    ->select('id_friend')
            )->pluck('my_id');
        $dataFriend = Connection::leftJoin('clients', 'clients.id', 'connections.id_client')
            ->leftJoin('roles', 'roles.id', 'connections.id_role')
            ->where('connections.id_group', $request->id_group)
            ->where('clients.fullname', 'like', $keyword)
            ->whereIn('clients.id', $friends)
            ->select('clients.fullname', 'clients.avatar', 'clients.id', 'roles.role_name as role', 'status')
            ->get();

        $dataOther = Connection::join('clients', 'clients.id', 'connections.id_client')
            ->leftJoin('roles', 'roles.id', 'connections.id_role')
            ->whereNotIn('clients.id', $friends)
            ->where('clients.id', '!=', $client->id)
            ->where('clients.fullname', 'like', $keyword)
            ->where('connections.id_group', $request->id_group)
            ->where('id_role', Role::member)
            ->select('clients.fullname', 'clients.avatar', 'clients.id', 'roles.role_name as role')
            ->get();
        $dataModerator = DB::table('connections')
            ->leftJoin('clients', 'clients.id', '=', 'connections.id_client')
            ->leftJoin('roles', 'roles.id', '=', 'connections.id_role')
            ->where('clients.fullname', 'like', $keyword)
            ->where('connections.id_group', $request->id_group)
            ->where(function ($query) {
                $query->where('id_role', Role::post_moderator)
                    ->orWhere('id_role', Role::member_moderator)
                    ->orWhere('id_role', Role::moderator);
            })
            ->select('clients.fullname', 'clients.avatar', 'clients.id', 'roles.role_name as role')
            ->get();
        return response()->json([
            'dataFriend'    => $dataFriend,
            'dataOther'    => $dataOther,
            'dataModerator'    => $dataModerator,

        ]);
    }
}
