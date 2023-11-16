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
                'password'          => bcrypt(123123),
                'email'             => 'conganh2122003@gmail.com',
                'phone_number'      => '0706085418',
                'fullname'          => 'Lê Công Anh',
                'avatar'            => 'avatar_male.jpg',
                'status'            => '1',
                'gender'            => Client::male,
                'nickname'          => 'Tôm',
                'address'           => '123 Đà Nẵng',
                'date_of_birth'     => '2002-06-02',
            ],
            [
                'username'          => 'minhquan',
                'password'          => bcrypt(123123),
                'email'             => 'quan@gmail.com',
                'phone_number'      => '0706085418',
                'fullname'          => 'Võ Minh Quân',
                'avatar'            => 'avatar_other.jpg',
                'status'            => '1',
                'gender'            => Client::other,
                'nickname'          => 'Quân',
                'address'           => '123 Đà Nẵng',
                'date_of_birth'     => '2002-06-02',
            ],
            [
                'username'          => 'huuhoang',
                'password'          => bcrypt(123123),
                'email'             => 'vuhuuhoang@gmail.com',
                'phone_number'      => '0706085418',
                'fullname'          => 'Vũ Hữu Hoàng',
                'avatar'            => 'avatar_female.jpg',
                'status'            => '1',
                'gender'            => Client::female,
                'nickname'          => 'Hoàng',
                'address'           => '123 Đà Nẵng',
                'date_of_birth'     => '2002-06-02',
            ],
            [
                'username'          => 'duylinh',
                'password'          => bcrypt(123123),
                'email'             => 'linh@gmail.com',
                'phone_number'      => '0706085418',
                'fullname'          => 'Nguyễn Ngọc Hoàng Duy Linh',
                'avatar'            => 'avatar_other.jpg',
                'status'            => '1',
                'gender'            => Client::other,
                'nickname'          => 'Linh',
                'address'           => '123 Đà Nẵng',
                'date_of_birth'     => '2002-06-02',
            ],
            [
                'username'          => 'tanhdeptrai',
                'password'          => bcrypt(123123),
                'email'             => 'phtsnh26@gmail.com',
                'phone_number'      => '0321321321',
                'fullname'          => 'Phan Công Tánh',
                'avatar'            => 'avatar_male.jpg',
                'status'            => '1',
                'gender'            => Client::male,
                'nickname'          => 'Tánh',
                'address'           => '321 Đà Nẵng',
                'date_of_birth'     => '2002-06-02',
            ],
            [
                'username'          => 'nhingu',
                'password'          => bcrypt(123123),
                'email'             => 'uyennhi@gmail.com',
                'phone_number'      => '0321321321',
                'fullname'          => 'Nguyễn Lê Uyển Nhi',
                'avatar'            => "avatar_female.jpg",
                'status'            => '1',
                'gender'            => Client::female,
                'nickname'          => 'Nhi Ngu',
                'address'           => '321 Đà Nẵng',
                'date_of_birth'     => '2002-06-02',
            ],
            [
                'username'          => 'thanhthuy',
                'password'          => bcrypt(123123),
                'email'             => 'thuy@gmail.com',
                'phone_number'      => '0321321321',
                'fullname'          => 'Huỳnh Thị Thanh Thủy',
                'avatar'            => "avatar_female.jpg",
                'status'            => '1',
                'gender'            => Client::female,
                'nickname'          => 'Tít',
                'address'           => '321 Đà Nẵng',
                'date_of_birth'     => '2002-06-02',
            ],
            [
                'username'          => 'thaonguyen',
                'password'          => bcrypt(123123),
                'email'             => 'phanthaonguyen@gmail.com',
                'phone_number'      => '0321321321',
                'fullname'          => 'Phan Thảo Nguyên',
                'avatar'            => "avatar_female.jpg",
                'status'            => '1',
                'gender'            => Client::female,
                'nickname'          => 'Zen',
                'address'           => '321 Đà Nẵng',
                'date_of_birth'     => '2002-06-02',
            ],
            [
                'username'          => 'huynhhieu',
                'password'          => bcrypt(123123),
                'email'             => 'huynhhieu@gmail.com',
                'phone_number'      => '0321321321',
                'fullname'          => 'Huỳnh Hiếu',
                'avatar'            => "avatar_male.jpg",
                'status'            => '1',
                'gender'            => Client::male,
                'nickname'          => 'Hiếu Ngu',
                'address'           => '321 Đà Nẵng',
                'date_of_birth'     => '2002-06-02',
            ],
            [
                'username'          => 'ductrong',
                'password'          => bcrypt(123123),
                'email'             => 'nguyenductrong@gmail.com',
                'phone_number'      => '0321321321',
                'fullname'          => 'Nguyễn Đức Trọng',
                'avatar'            => "avatar_female.jpg",
                'status'            => '1',
                'gender'            => Client::female,
                'nickname'          => 'Trọng',
                'address'           => '321 Đà Nẵng',
                'date_of_birth'     => '2002-06-02',
            ],
        ]);
    }
}
