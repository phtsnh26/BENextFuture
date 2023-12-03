<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\Friend;
use App\Models\Group;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function data_all_group()
    {
        $discover = Group::all();
        return response()->json([
            'data' => $discover,
        ]);
    }
    public function data_your_group(Request $request)
    {
        // SELECT connections.* from groups
        // join connections on connections.id_group = groups.id
        // where id_client = 2
        $client = $request->user();
        $your_group = Group::join('connections', 'connections.id_group', 'groups.id')
            ->where('id_client', $client->id)
            ->where('id_role', Role::admin)
            ->select('groups.*')
            ->get();
        foreach ($your_group as $key => $value) {
            $getMember = Connection::where('id_group', $value->id)
                ->groupBy('id_group')
                ->select(DB::raw('count(*) as member'))
                ->first(); // Sử dụng first() để lấy một dòng duy nhất từ câu truy vấn
            $your_group[$key]->member = $getMember->member;
        }
        return response()->json([
            'data' => $your_group,
        ]);
    }
    public function data_group_participated(Request $request)
    {
        // SELECT  groups.* from groups
        // join connections on connections.id_group = groups.id
        // where id_client = 2
        $client = $request->user();
        $group_participated = Group::join('connections', 'connections.id_group', 'groups.id')
            ->where('id_client', $client->id)
            ->select('groups.*')
            ->get();
        foreach ($group_participated as $key => $value) {
            $getMember = Connection::where('id_group', $value->id)
                ->groupBy('id_group')
                ->select(DB::raw('count(*) as member'))
                ->first(); // Sử dụng first() để lấy một dòng duy nhất từ câu truy vấn
            $group_participated[$key]->member = $getMember->member;
        }
        return response()->json([
            'data' => $group_participated,
        ]);
    }
    public function createGroup(Request $request)
    {
        $client = $request->user();
        try {
            if ($client) {
                DB::beginTransaction();
                $create_group = Group::create([
                    'group_name' => $request->name_group,
                    'cover_image' => "cover/cover_image.png",
                    'privacy' => $request->privacy,
                    'display' => $request->display,
                ]);
                $connection = Connection::create([
                    'id_role' => Role::admin,
                    'id_client' => $client->id,
                    'id_group' => $create_group->id,
                ]);
                $id_invites = $request->id_invites;
                foreach ($id_invites as $key => $value) {
                    Connection::create([
                        'id_role' => Role::newbie,
                        'id_client' => $value,
                        'id_group' => $create_group->id,
                    ]);
                }
                if ($connection) {
                    DB::commit();
                    return response()->json([
                        'status'    => 1,
                        'message'   => 'Created group successfully',
                    ]);
                }
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => 'Create group erorr',
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'    => 0,
                'message'   => $th,
            ]);
        }
    }
    public function dataInvite(Request $request)
    {
        $search = '%' . $request->value . "%";
        $client = $request->user();
        $friends = Friend::select('clients.*')
            ->from(function ($query) use ($client) {
                $query->select('id_friend as result_id')
                    ->from('friends')
                    ->where('my_id', $client->id)
                    ->union(
                        DB::table('friends')
                            ->select('my_id as result_id')
                            ->where('id_friend', $client->id)
                    );
            }, 'new')
            ->join('clients', 'clients.id', '=', 'new.result_id')
            ->where('clients.fullname', 'like', $search)
            ->whereNotIn('clients.id', $request->id_invites)
            ->get();

        return response()->json([
            'friends'    => $friends,
            'ids' => $request->all()
        ]);
    }
}
