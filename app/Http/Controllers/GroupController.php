<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Connection;
use App\Models\Follower;
use App\Models\Friend;
use App\Models\Group;
use App\Models\Notification;
use App\Models\RequestGroup;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Nette\Utils\Random;

class GroupController extends Controller
{
    public function data_all_group(Request $request)
    {
        $client = $request->user();
        $group_participated = Group::join('connections', 'connections.id_group', 'groups.id')
            ->where('id_client', $client->id)
            ->select('groups.id')
            ->pluck('groups.id');
        $discover = Group::whereNotIn('id', $group_participated)
            ->where('display', Group::visible)
            ->get();

        foreach ($discover as $key => $value) {
            $getMember = Connection::where('id_group', $value->id)
                ->groupBy('id_group')
                ->select(DB::raw('count(*) as member'))
                ->first(); // Sử dụng first() để lấy một dòng duy nhất từ câu truy vấn
            $discover[$key]->member = $getMember->member;
        }
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
            ->where('id_role', "!=", Role::admin)
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
    public function dataAllGroupParticipated(Request $request)
    {
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
                    RequestGroup::create([
                        'id_client'     => $client->id,
                        'id_group'      => $create_group->id,
                        'id_invite'     => $value,
                        'status'        => RequestGroup::invite,
                    ]);
                    Notification::create([
                        'id_client'     => $value,
                        'my_id'         => $client->id,
                        'id_group'      => $create_group->id,
                        'type'          => Notification::invite_group,
                    ]);
                }
                if ($connection) {
                    DB::commit();
                    return response()->json([
                        'status'    => 1,
                        'id_group'   => $create_group->id,
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

    public function infoGroup($id_group, Request $request)
    {
        $info = Group::find($id_group);
        $member = Connection::where('id_group', $info->id)->select('id_client')->pluck('id_client');
        $info->member = $member->count();
        $info_members = Client::whereIn('id', $member)->inRandomOrder()->limit(12)->get();
        foreach ($info_members as $key => $value) {
            $mutual = array_intersect(Client::getFriend($value['id']), Client::getFriend($request->user()->id));
            $info_members[$key]->mutual = count($mutual);
            $friends = [];

            if (count($mutual) >= 2) {

                for ($i = 0; $i < 2; $i++) {
                    $rand  = array_rand($mutual);
                    $infoClient = Client::find($mutual[$rand]);
                    array_push($friends, $infoClient);
                    unset($mutual[$rand]);
                }
            } else if (count($mutual) == 1) {
                $infoClient = Client::find($mutual[array_rand($mutual)]);
                array_push($friends, $infoClient);
            } else {
                $follower = DB::table('followers')
                    ->select('my_id', DB::raw('count(my_id) as count'))
                    ->where('my_id', $value['id'])
                    ->groupBy('my_id')
                    ->get();

                $info_members[$key]->follower = $follower[0]->count;
            }
            $info_members[$key]->friends = $friends;
        }
        return response()->json([
            'info'    => $info,
            'member'    => $info_members

        ]);
    }
    public function dataInviteDetail(Request $request)
    {
        $inGroup = Connection::where('id_group', $request->id_group)->pluck('id_client');
        $client = $request->user();

        $friend = Friend::where('my_id', $client->id)
            ->select('id_friend as result_id')
            ->union(
                Friend::where('id_friend', $client->id)
                    ->select('my_id as result_id')
            )
            ->get();
        $data = Client::whereIn('id', $friend)->whereNotIn('id', $inGroup)->get();

        return response()->json([
            'data'    => $data,
        ]);
    }
    public function sendInvite(Request $request)
    {
        try {
            DB::beginTransaction();
            $client = $request->user();
            foreach ($request->id_invites as $key => $value) {
                $check = RequestGroup::where('id_invite', $value['id'])
                    ->where('id_group', $request->id_group)->first();
                if ($check) {
                    $check->update([
                        'created_at' => Carbon::now(),
                    ]);
                    Notification::where('id_client', $value['id'])
                        ->where('id_group', $request->id_group)
                        ->where('my_id', $client->id)
                        ->update([
                            'created_at' => Carbon::now(),
                        ]);
                } else {
                    RequestGroup::create([
                        'id_client'     => $client->id,
                        'id_group'      => $request->id_group,
                        'id_invite'     => $value['id'],
                        'status'        => RequestGroup::invite,
                    ]);
                    Notification::create([
                        'id_client'     => $value['id'],
                        'my_id'         => $client->id,
                        'id_group'      => $request->id_group,
                        'type'          => Notification::invite_group,
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'status'    => 1,
                'message'   => 'invitation sent successfully!',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json([
                'status'    => 0,
                'message'   => $th,
            ]);
        }
    }
    public function comeInGroup(Request $request)
    {
        try {
            DB::beginTransaction();
            $client = $request->user();
            $check = RequestGroup::where('id_group', $request->id)
                ->where('id_invite', $client->id)->first();
            if (!$check) {
                if (Group::find($request->id)->join_approval == Group::requiredGroupApproval) {
                    RequestGroup::create([
                        'id_group' => $request->id,
                        'id_invite' => $client->id,
                        'status' => RequestGroup::come,
                    ]);
                } else {
                    Connection::create([
                        'id_client' => $client->id,
                        'id_group' => $request->id,
                        'id_role' => Role::member,
                    ]);
                }
            } else {
                if (Group::find($request->id)->join_approval == Group::requiredGroupApproval) {
                    $check->touch();
                } else {
                    Connection::create([
                        'id_client' => $client->id,
                        'id_group' => $request->id,
                        'id_role' => Role::member,
                    ]);
                    $del = RequestGroup::where('id_invite', $client->id)
                        ->where('id_group', $request->id)
                        ->first();
                    $del->delete();
                }
            }
            DB::commit();
            return response()->json([
                'status'    => 1,
                'message'   => 'You have successfully sent your request!',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'    => 0,
                'message'   => 'Send request failed!',
            ]);
        }
    }
    public function getData(Request $request)
    {
        $data = Group::find($request->id_group);
        return response()->json([
            'data'    => $data,
        ]);
    }
    public function updatePrivacy(Request $request)
    {
        try {
            DB::beginTransaction();
            $group = Group::find($request->id_group);
            $privacy = $group->privacy != Group::public ? Group::public : Group::private;
            Group::find($request->id_group)->update([
                'privacy' => $privacy
            ]);
            DB::commit();
            if ($privacy != Group::public) {
                return response()->json([
                    'status'    => 1,
                    'message'   => 'This group will be private from now on',
                ]);
            } else {
                return response()->json([
                    'status'    => 1,
                    'message'   => 'This group will be public from now on',
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
    public function updateDisplay(Request $request)
    {
        try {
            DB::beginTransaction();
            $group = Group::find($request->id_group);
            $display = $group->display  != Group::visible ? Group::visible : Group::hidden;

            Group::find($request->id_group)->update([
                'display' => $display
            ]);
            DB::commit();
            if ($display == Group::visible)
                $mess = "";

            return response()->json([
                'status'    => 1,
                'message'   => 'oke',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'    => 0,
                'message'   => $th,
            ]);
        }
    }
    public function updateJoinApproval(Request $request)
    {
        try {
            DB::beginTransaction();
            $group = Group::find($request->id_group);
            $group->update([
                'join_approval' => $request->join_approval
            ]);
            DB::commit();


            if ($request->join_approval == Group::requiredGroupApproval) {
                return response()->json([
                    'status'    => 1,
                    'message'   => 'From now on, applications to join the group need to be approved!',
                ]);
            } else {
                return response()->json([
                    'status'    => 1,
                    'message'   => 'From now on you can directly join the group!',
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
    public function updatePostApproval(Request $request)
    {
        try {
            DB::beginTransaction();
            $group = Group::find($request->id_group);
            $group->update([
                'post_approval' => $request->post_approval
            ]);

            DB::commit();
            if ($request->post_approval == true) {
                return response()->json([
                    'status'    => 1,
                    'message'   => 'From now on, applications to post in the group need to be approved!',
                ]);
            } else {
                return response()->json([
                    'status'    => 1,
                    'message'   => 'From now on you can directly post in the group!',
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
    public function dataInvited(Request $request)
    {
        $client = $request->user();
        $data_invited = DB::table(DB::raw('(select rg.id_client, rg.status, rg.id_group, c.fullname, rg.id_invite, rg.created_at
                                            from request_groups rg
                                            left join clients c on c.id = rg.id_invite) as a'))
            ->select('a.*', 'clients.fullname', 'groups.group_name', 'groups.cover_image')
            ->leftJoin('clients', 'clients.id', '=', 'a.id_client')
            ->leftJoin('groups', 'groups.id', '=', 'a.id_group')
            ->where('a.status', '=', RequestGroup::invite)
            ->where('id_invite', $client->id)
            ->orderByDesc('a.created_at')
            ->get();

        return response()->json([
            'status'    => 1,
            'data_invited'      => $data_invited,
        ]);
    }
    public function dataComeInGroup(Request $request)
    {
        // SELECT clients.fullname,clients.avatar,clients.id,request_groups.created_at
        // FROM (request_groups
        // join clients on clients.id  = request_groups.id_invite)
        // where request_groups.status = 1 and request_groups.id_group = 3

        $comeIn = RequestGroup::join('clients', 'clients.id', 'request_groups.id_invite')
            ->where('request_groups.status', RequestGroup::come)
            ->where('request_groups.id_group', $request->id_group)
            ->select('clients.fullname', 'clients.avatar', 'request_groups.updated_at as created_at', 'clients.id')
            ->orderByDesc('created_at')
            ->get();
        foreach ($comeIn as $key => $value) {
            $groupParticipated = Connection::where('id_client', $value['id'])->get();
            $comeIn[$key]->groupParticipated = $groupParticipated->count();
        }
        return response()->json([
            'data' => $comeIn,
        ]);
    }
    public function dataMember(Request $request)
    {
        $client = $request->user();
        $friends = Friend::where('id_friend', $client->id)
            ->select('my_id')
            ->Union(
                Friend::where('my_id', $client->id)
                    ->select('id_friend')
            )->pluck('my_id');
        $members = Connection::join('clients', 'clients.id', 'connections.id_client')
            ->leftJoin('roles', 'roles.id', 'connections.id_role')
            ->whereNotIn('clients.id', $friends)
            ->where('clients.id', '!=', $client->id)
            ->where('connections.id_group', $request->id_group)
            ->where('id_role', Role::member)
            ->select('clients.fullname', 'clients.username', 'clients.avatar', 'clients.id', 'roles.role_name as role')
            ->get();
        foreach ($members as $key => $value) {
            $check = Follower::where('id_follower', $value['id'])->where('my_id', $client->id)->first();
            if ($check) {
                $members[$key]->status = 1;
            } else {
                $members[$key]->status = 0;
            }
        }
        $count = Connection::where('id_group', $request->id_group)->get();
        return response()->json([
            'data' => $members,
            'count' => $count->count(),
        ]);
    }
    public function dataMemberFriend(Request $request)
    {
        $client = $request->user();
        $friends = Friend::where('id_friend', $client->id)
            ->select('my_id')
            ->Union(
                Friend::where('my_id', $client->id)
                    ->select('id_friend')
            )->pluck('my_id');
        $members = Connection::leftJoin('clients', 'clients.id', 'connections.id_client')
            ->leftJoin('roles', 'roles.id', 'connections.id_role')
            ->where('connections.id_group', $request->id_group)
            ->whereIn('clients.id', $friends)
            ->select('clients.fullname', 'clients.username', 'clients.avatar', 'clients.id', 'roles.role_name as role', 'status')
            ->get();
        return response()->json([
            'data' => $members,
        ]);
    }
    public function dataAdmin(Request $request)
    {
        $members = Connection::leftJoin('clients', 'clients.id', 'connections.id_client')
            ->leftJoin('roles', 'roles.id', 'connections.id_role')
            ->where('connections.id_group', $request->id_group)
            ->where('id_role', Role::admin)
            ->select('clients.fullname', 'clients.username', 'clients.avatar', 'clients.id', 'roles.role_name as role', 'connections.id_role')
            ->get();
        foreach ($members as $key => $value) {
            $check = Follower::where('id_follower', $value['id'])->where('my_id', $request->user()->id)->first();
            if ($check) {
                $members[$key]->status = 1;
            } else {
                $members[$key]->status = 0;
            }
        }
        return response()->json([
            'data' => $members,
        ]);
    }
    public function dataModeration(Request $request)
    {

        $members = Connection::leftJoin('clients', 'clients.id', 'connections.id_client')
            ->leftJoin('roles', 'roles.id', 'connections.id_role')
            ->whereIn('id_role', [Role::post_moderator, Role::member_moderator, Role::moderator])
            ->where('connections.id_group', $request->id_group)
            ->select('clients.fullname', 'clients.username', 'clients.avatar', 'clients.id', 'roles.role_name as role')
            ->get();
        foreach ($members as $key => $value) {
            $check = Follower::where('id_follower', $value['id'])->where('my_id', $request->user()->id)->first();
            if ($check) {
                $members[$key]->status = 1;
            } else {
                $members[$key]->status = 0;
            }
        }
        return response()->json([
            'data' => $members,
        ]);
    }
    public function dataPopularGroup(Request $request)
    {
        $client = $request->user();
        $groups = Group::whereNotIn('id', function ($query) use ($client) {
            $query->select('id_group')
                ->from('connections')
                ->where('id_client', $client->id);
        })->get();
        foreach ($groups as $key => $value) {

            $getMember = Connection::where('id_group', $value->id)
                ->groupBy('id_group')
                ->select(DB::raw('count(*) as member'))
                ->first(); // Sử dụng first() để lấy một dòng duy nhất từ câu truy vấn
            $groups[$key]->member = $getMember->member;

            $connection = Connection::where('id_group', $value->id)->pluck('id_client')->toArray();
            $mutual = array_intersect(Client::getFriend($client->id), $connection);

            $groups[$key]->mutual = count($mutual);
        }
        $sortedGroups = $groups->sortByDesc('mutual')->take(6);

        // Shuffle the sorted groups randomly
        $groupRandom = $sortedGroups->shuffle();

        return response()->json([
            'dataPopular' => $groupRandom,
        ]);
    }

    public function renameGroup(Request $request)
    {
        $group = Group::find($request->id_group);
        $group->group_name = $request->group_name;
        $group->save();
        return response()->json([
            'status' =>  1,
            'group_name' => $group,
            'message'   => 'Rename group successfully',
        ]);
    }
    public function updateAnonymity(Request $request)
    {
        $group = Group::find($request->id_group);
        $group->anonymity = $request->anonymity;
        $group->save();
        return response()->json([
            'status' =>  1,
            'group_name' => $group,
            'message'   => 'Update group successfully',
        ]);
    }
}
