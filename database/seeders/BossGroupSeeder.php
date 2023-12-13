<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BossGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("connections")->delete();
        DB::table("connections")->truncate();
        DB::table("connections")->insert([
            [
                'id_client'     => 1,
                'id_group'      => 1,
                'id_role'       => 2,
            ],
            [
                'id_client'     => 5,
                'id_group'      => 2,
                'id_role'       => 2,
            ],
        ]);
        for ($i = 3; $i <= 22; $i++) {
            DB::table("connections")->insert([
                [
                    'id_client'     => rand(1, 38),
                    'id_group'      => $i,
                    'id_role'       => 2,
                ],
            ]);
        }
    }
}
