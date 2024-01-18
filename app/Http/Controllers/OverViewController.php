<?php

namespace App\Http\Controllers;

use App\Models\PostGroup;
use App\Models\RequestGroup;
use Illuminate\Http\Request;

class OverViewController extends Controller
{
    public function dataOverview(Request $request)
    {
        $data_over_view_request_membership = RequestGroup::where('id_group', $request->id_group)
            ->where('status', RequestGroup::come)
            ->get();

        $data_over_view_post = PostGroup::where('id_group', $request->id_group)
            ->where('status', PostGroup::PENDING)
            ->get();
        $data_over_view_one_day_request_membership = RequestGroup::where('id_group', $request->id_group)
            ->where('status', RequestGroup::come)
            ->whereDate('updated_at', '>=', now()->subDays(1))
            ->get();
        $data_over_view_one_day_post = PostGroup::where('id_group', $request->id_group)
            ->where('status', PostGroup::PENDING)
            ->whereDate('updated_at', '>=', now()->subDays(1))
            ->get();
        return response()->json([
            'data_over_view'    => count($data_over_view_request_membership),
            'data_over_view_one_day'   => count($data_over_view_one_day_request_membership),
            'data_over_view_post'   => count($data_over_view_post),
            'data_over_view_one_day_post'   => count($data_over_view_one_day_post),
        ]);
    }
}
