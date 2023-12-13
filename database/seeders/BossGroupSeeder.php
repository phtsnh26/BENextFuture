<?php

namespace Database\Seeders;

use Carbon\Carbon;
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
                'created_at'    => '2023-6-13 11:54:39'
            ],
            [
                'id_client'     => 5,
                'id_group'      => 2,
                'id_role'       => 2,
                'created_at'    => '2023-6-13 11:54:39'
            ],
        ]);
        $randomDays = mt_rand(100, 1000);
        for ($i = 3; $i <= 22; $i++) {
            DB::table("connections")->insert([
                [
                    'id_client'     => rand(1, 38),
                    'id_group'      => $i,
                    'id_role'       => 2,
                    'created_at'    => Carbon::now()->subDays($randomDays),

                ],
            ]);
        }
    }
}
