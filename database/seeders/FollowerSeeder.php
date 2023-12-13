<?php

namespace Database\Seeders;

use App\Models\Follower;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FollowerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("followers")->delete();
        DB::table("followers")->truncate();
        for ($i = 0; $i < 500; $i++) {
            Follower::create(
                [
                    'my_id' => rand(1, 38),
                    'id_follower' => rand(1, 38),
                    'status' => Follower::friend_request,
                ],
            );
        }
        $duplicateRecords = Follower::select('my_id', 'id_follower')
            ->groupBy('my_id', 'id_follower')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        // Xoá các bản ghi trùng nhau, chỉ giữ lại một bản ghi duy nhất
        foreach ($duplicateRecords as $record) {
            Follower::where('my_id', $record->my_id)
                ->where('id_follower', $record->id_follower)
                ->limit($record->count - 1)
                ->delete();
        }
    }
}
