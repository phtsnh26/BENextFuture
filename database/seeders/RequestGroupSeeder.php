<?php

namespace Database\Seeders;

use App\Models\Connection;
use App\Models\Notification;
use App\Models\RequestGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequestGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("request_groups")->truncate();
        DB::table("request_groups")->delete();
        DB::table("notifications")->truncate();
        DB::table("notifications")->delete();
        for ($i = 0; $i < 600; $i++) {
            $check = rand(1, 100);

            if ($check >= 50) {
                $id_client = rand(1, 38);
            } else {
                $id_client = null;
            }

            $idGroup = rand(1, 22);
            $idInvite = rand(1, 38);

            $checkContain = RequestGroup::where('id_invite', $idInvite)
                ->where('id_group', $idGroup)
                ->first();
            $checkConnectContain = Connection::where('id_client', $idInvite)->where('id_group', $idGroup)->first();

            if (!$checkContain && !$checkConnectContain) {
                RequestGroup::create([
                    'id_client'     => $id_client,
                    'id_group'      => $idGroup,
                    'id_invite'     => $idInvite,
                    'status'        => $id_client ? RequestGroup::invite : RequestGroup::come,
                ]);
                if ($id_client) {

                    Notification::create([
                        'id_client'         => $idInvite,
                        'my_id'             => $id_client,
                        'id_group'          => $idGroup,
                        'type'              => Notification::invite_group,
                    ]);
                }
            }
        }
    }
}
