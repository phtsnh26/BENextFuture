<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("clients")->delete();
        DB::table("clients")->truncate();
        DB::table("clients")->insert([
            [
                'username'          => 'conganh212',
                'password'          => '123123',
                'email'             => 'conganh2122003@gmail.com',
                'phone_number'      => '0706085418',
                'fullname'          => 'Lê Công Anh',
                'avatar'            => 'img/avt_cv',
                'status'            => '1',
                'gender'            => Client::male,
                'nickname'          => 'Tôm',
                'address'           => '123 Đà Nẵng',
            ],
            [
                'username'          => 'tanhdeptrai',
                'password'          => '123123',
                'email'             => 'phtsnh26@gmail.com',
                'phone_number'      => '0321321321',
                'fullname'          => 'Phan Công Tánh',
                'avatar'            => 'img/avt2',
                'status'            => '1',
                'gender'            => Client::male,
                'nickname'          => 'Tánh',
                'address'           => '321 Đà Nẵng',
            ],
            [
                'username'          => 'nguyenthibep',
                'password'          => '123123',
                'email'             => 'nguyenthibep@gmail.com',
                'phone_number'      => '0321321321',
                'fullname'          => 'Nguyễn Thị Bẹp',
                'avatar'            => "https://i.pinimg.com/736x/e1/8b/85/e18b859ca7d8805f6380ea077d9d9e7e.jpg",
                'status'            => '1',
                'gender'            => Client::female,
                'nickname'          => 'Bẹp',
                'address'           => '321 Đà Nẵng',
            ],
        ]);
    }
}
