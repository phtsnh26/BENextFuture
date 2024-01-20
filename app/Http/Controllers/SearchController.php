<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Connection;
use App\Models\Group;
use App\Models\PostGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function searchNav(Request $request)
    {
        $client = $request->user();
        $keySearch = '%' . strtolower($request->keySearch) . '%';
        $dataClient = Client::where(function ($query) use ($keySearch) {
            $query->where('username', 'like', $keySearch)
                ->orWhere('fullname', 'like', $keySearch)
                ->orWhere('nickname', 'like', $keySearch);
        })
            ->where('id', '!=', $client->id)
            ->limit(5)
            ->get();
        $dataGroup = Group::where('group_name', 'like', $keySearch)
            ->limit(5)->get();
        return response()->json([
            'dataSearchClient' => $dataClient,
            'dataSearchGroup' => $dataGroup
        ]);
    }
    public function search(Request $request)
    {

        if ($request->type == 'all') {
            $LIMIT_CLIENT = 5;
            $LIMIT_GROUP = 5;
        } else if ($request->type == 'group') {
            $LIMIT_CLIENT = 0;
            $LIMIT_GROUP = 99;
        } else {
            $LIMIT_GROUP = 0;
            $LIMIT_CLIENT = 99;
        }
        $client = $request->user();
        $id = $client->id;
        $keySearch = '%' . strtolower($request->keySearch) . '%';
        $dataClient = DB::table('clients as c')
            ->select('c.id', 'c.username', 'c.fullname', 'c.avatar', 'c.address', 'c.nickname')
            ->selectRaw("
                CASE
                    WHEN c.id IN (
                        SELECT id_friend
                        FROM friends
                        WHERE my_id = ?
                        UNION
                        SELECT my_id
                        FROM friends
                        WHERE id_friend = ?
                    ) THEN 'a'
                    WHEN c.id IN (
                        SELECT my_id
                        FROM followers
                        WHERE id_follower = ?
                    ) THEN 'b'
                    ELSE 'c'
                END as relationship
            ", [$id, $id, $id])
            ->where(function ($query) use ($keySearch) {
                $query->where('c.username', 'like', '%' . $keySearch . '%')
                    ->orWhere('c.fullname', 'like', '%' . $keySearch . '%')
                    ->orWhere('c.nickname', 'like', '%' . $keySearch . '%');
            })
            ->where('c.id', '!=', $id)
            ->where('c.status', 1)
            ->orderBy('relationship')
            ->limit($LIMIT_CLIENT)
            ->get();

        foreach ($dataClient as $key => $value) {
            $mutual = array_intersect(Client::getFriend($value->id), Client::getFriend($client->id));
            $dataClient[$key]->mutual = count($mutual);
        }
        $dataGroup = Group::where('group_name', 'like', $keySearch)
            ->limit($LIMIT_GROUP)->get();

        foreach ($dataGroup as $key => $value) {
            $checkJoinGroup = Connection::where('id_group', $value->id)
                ->where('id_client', $client->id)
                ->first();

            if (!$checkJoinGroup) {
                if ($value->display === Group::hidden) {
                    unset($dataGroup[$key]);
                } else {
                    $getMember = Connection::where('id_group', $value->id)
                        ->groupBy('id_group')
                        ->select(DB::raw('count(*) as member'))
                        ->first();

                    $getPost = PostGroup::where('id_group', $value->id)->count();

                    $dataGroup[$key]->member = $getMember->member;
                    $dataGroup[$key]->posts = $getPost;
                    $dataGroup[$key]->checkJoin = false;
                }
            } else {
                $getMember = Connection::where('id_group', $value->id)
                    ->groupBy('id_group')
                    ->select(DB::raw('count(*) as member'))
                    ->first();

                $getPost = PostGroup::where('id_group', $value->id)->count();

                $dataGroup[$key]->member = $getMember->member;
                $dataGroup[$key]->posts = $getPost;
                $dataGroup[$key]->checkJoin = true;
            }
        }
        $dataGroup = $dataGroup->sortByDesc('checkJoin')->values();
        return response()->json([
            'dataSearchClient' => $dataClient,
            'dataSearchGroup' => $dataGroup
        ]);
    }
}
