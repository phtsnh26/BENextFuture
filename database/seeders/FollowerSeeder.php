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
        for ($i = 0; $i < 400; $i++) {
            $my_id = rand(1, 38);
            $id_follower = rand(1, 38);
            $check = Follower::where(function ($query) use ($my_id, $id_follower) {
                $query->where('my_id', $my_id)
                    ->where('id_follower', $id_follower);
            })
                ->orWhere(function ($query) use ($my_id, $id_follower) {
                    $query->where('my_id', $id_follower)
                        ->where('id_follower', $my_id);
                })
                ->first();
            if ($check || $my_id == $id_follower) {
                continue;
            } else {
                Follower::create(
                    [
                        'my_id' => $my_id,
                        'id_follower' => $id_follower,
                        'status' => Follower::friend_request,
                    ],
                );
            }
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
