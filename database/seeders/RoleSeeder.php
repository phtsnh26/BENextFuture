<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("roles")->delete();
        DB::table("roles")->truncate();
        DB::table("roles")->insert([
            [
                'role_name' => 'Member'
            ],
            [
                'role_name' => 'Admin'
            ],
            [
                'role_name' => 'Post moderator'
            ],
            [
                'role_name' => 'Member moderator'
            ],
            [
                'role_name' => 'Moderator'
            ],
        ]);
    }
}
