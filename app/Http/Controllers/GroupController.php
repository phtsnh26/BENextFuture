<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\Group;
use App\Models\Role;
use Illuminate\Http\Request;

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
            ->get();
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
            ->get();
        return response()->json([
            'data' => $group_participated,
        ]);
    }
    public function createGroup(Request $request)
    {
        $client = $request->user();
        if ($client) {
            $create_group = Group::create([
                'name_group' => $request->name_group,
                'cover_image' => $request->cover_image,
                'privacy' => $request->privacy,
                'display' => $request->display,
            ]);
            $connection = Connection::create([
                'id_role' => Role::admin,
                'id_client' => $client->id,
                'id_group' => $create_group->id,
            ]);
            if ($connection) {
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
    }
}
