<?php

namespace Database\Seeders;

use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("groups")->delete();
        DB::table("groups")->truncate();
        DB::table("groups")->insert([
            [
                'group_name'      => "Marvel",
                'cover_image'     => "cover/marvel.jpg",
                'privacy'         => Group::public,
                'display'         => Group::visible,
                'join_approval'   => true,
                'post_approval'   => true,
            ],
            [
                'group_name'      => "Mini World",
                'cover_image'     => "cover/miniworld.jpg",
                'privacy'         => Group::public,
                'display'         => Group::visible,
                'join_approval'   => true,
                'post_approval'   => true,

            ],
            [
                'group_name'      => "Liên Minh Huyền Thoại",
                'cover_image'     => "cover/lol.jpg",
                'privacy'         => Group::public,
                'display'         => Group::visible,
                'join_approval'   => true,
                'post_approval'   => true,

            ],
            [
                'group_name'      => "Cộng đồng NextJS",
                'cover_image'     => "cover/nextjs.png",
                'privacy'         => Group::public,
                'display'         => Group::visible,
                'join_approval'   => true,
                'post_approval'   => true,

            ],
            [
                'group_name'      => "Hội thiểu năng",
                'cover_image'     => "cover/cover_image.png",
                'privacy'         => Group::public,
                'display'         => Group::visible,
                'join_approval'   => true,
                'post_approval'   => true,

            ],
            [
                'group_name'      => "DKU Group",
                'cover_image'     => "cover/cover_image.png",
                'privacy'         => Group::private,
                'display'         => Group::visible,
                'join_approval'   => true,
                'post_approval'   => true,

            ],
            [
                'group_name'      => "Learn HTML, CSS and JavaScript",
                'cover_image'     => "cover/learn_html.jpg",
                'privacy'         => Group::private,
                'display'         => Group::hidden,
                'join_approval'   => true,
                'post_approval'   => true,

            ], [
                'group_name'      => "Tech Enthusiasts",
                'cover_image'     => "cover/cover_image.png",
                'privacy'         => Group::public,
                'display'         => Group::visible,
                'join_approval'   => true,
                'post_approval'   => true,

            ],
            [
                'group_name'      => "Photography Lovers",
                'cover_image'     => "cover/cover_image.png",
                'privacy'         => Group::public,
                'display'         => Group::visible,
                'join_approval'   => false,
                'post_approval'   => true,

            ],
            [
                'group_name'      => "Cooking Enthusiasts",
                'cover_image'     => "cover/cook_group.jpg",
                'privacy'         => Group::private,
                'display'         => Group::visible,
                'join_approval'   => true,
                'post_approval'   => false,
            ],
            [
                'group_name'      => "Travel Adventures",
                'cover_image'     => "cover/cover_image.png",
                'privacy'         => Group::private,
                'display'         => Group::hidden,
                'join_approval'   => true,
                'post_approval'   => false,
            ],
            [
                'group_name'      => "Coding Challenges",
                'cover_image'     => "cover/cover_image.png",
                'privacy'         => Group::public,
                'display'         => Group::visible,
                'join_approval'   => false,
                'post_approval'   => false,
            ], [
                'group_name'      => "Gaming Community",
                'cover_image'     => "cover/cover_image.png",
                'privacy'         => Group::private,
                'display'         => Group::hidden,
                'join_approval'   => true,
                'post_approval'   => true,

            ],
            [
                'group_name'      => "Book Club",
                'cover_image'     => "cover/book_group.jpg",
                'privacy'         => Group::public,
                'display'         => Group::visible,
                'join_approval'   => true,
                'post_approval'   => false,
            ],
            [
                'group_name'      => "Fitness Enthusiasts",
                'cover_image'     => "cover/cover_image.png",
                'privacy'         => Group::public,
                'display'         => Group::visible,
                'join_approval'   => false,
                'post_approval'   => true,

            ],
            [
                'group_name'      => "Artists' Corner",
                'cover_image'     => "cover/cover_image.png",
                'privacy'         => Group::private,
                'display'         => Group::visible,
                'join_approval'   => false,
                'post_approval'   => false,


            ],
            [
                'group_name'      => "Film Buffs",
                'cover_image'     => "cover/film_group.jpg",
                'privacy'         => Group::private,
                'display'         => Group::hidden,
                'join_approval'   => true,
                'post_approval'   => true,

            ],
            [
                'group_name'      => "Science Enthusiasts",
                'cover_image'     => "cover/cover_image.png",
                'privacy'         => Group::public,
                'display'         => Group::visible,
                'join_approval'   => (bool) rand(0, 1),
                'post_approval'   => (bool) rand(0, 1),

            ],
            // Dòng 2
            [
                'group_name'      => "Music Lovers",
                'cover_image'     => "cover/music_group.jpg",
                'privacy'         => Group::private,
                'display'         => Group::visible,
                'join_approval'   => (bool) rand(0, 1),
                'post_approval'   => (bool) rand(0, 1),

            ],
            // Dòng 3
            [
                'group_name'      => "Fitness Challenges",
                'cover_image'     => "cover/gym_group.jpg",
                'privacy'         => Group::public,
                'display'         => Group::visible,
                'join_approval'   => (bool) rand(0, 1),
                'post_approval'   => (bool) rand(0, 1),

            ],
            // Dòng 4
            [
                'group_name'      => "Artisans' Guild",
                'cover_image'     => "cover/cover_image.png",
                'privacy'         => Group::private,
                'display'         => Group::hidden,
                'join_approval'   => (bool) rand(0, 1),
                'post_approval'   => (bool) rand(0, 1),

            ],
            // Dòng 5
            [
                'group_name'      => "Movie Buff Club",
                'cover_image'     => "cover/cover_image.png",
                'privacy'         => Group::public,
                'display'         => Group::visible,
                'join_approval'   => (bool) rand(0, 1),
                'post_approval'   => (bool) rand(0, 1),

            ],

        ]);
        $groups = Group::all();
        foreach ($groups as $key => $value) {
            $value->created_at = Carbon::now()->subDays(rand(365, 3 * 365));
            $value->save();
        }
    }
}
