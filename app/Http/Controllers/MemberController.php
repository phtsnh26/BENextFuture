<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function removeMember(Request $request)
    {
        $remove = Connection::where('id_client', $request->id)
            ->where('id_group', $request->id_group)
            ->first();
        if ($remove) {
            $remove->delete();
        }
        return response()->json([
            'status'    => 1,
            'message'   => "Remove member successfully"
        ]);
    }
}
