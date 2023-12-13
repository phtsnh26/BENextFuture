<?php

namespace Database\Seeders;

use App\Models\Connection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConnectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 500; $i++) {
            $randomValue = rand(1, 100);
            if ($randomValue <= 70) {
                $role = 1;
            } else {
                $role = rand(3, 5);
            }
            $id_client = rand(1, 38);
            $id_group = rand(1, 22);
            $check = Connection::where('id_client', $id_client)
                ->where('id_group', $id_group)
                ->first();
            if (!$check) {
                Connection::create(
                    [
                        'id_client'     => $id_client,
                        'id_group'      => $id_group,
                        'id_role'       => $role,
                    ],
                );
            }
        }
    }
}
