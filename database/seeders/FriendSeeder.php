<?php

namespace Database\Seeders;

use App\Models\Follower;
use App\Models\Friend;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FriendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("friends")->truncate();
        DB::table("friends")->delete();

        // Thêm dữ liệu vào bảng Friend
        for ($i = 0; $i < 400; $i++) {
            $myId = rand(1, 38);
            $idFriend = rand(1, 38);

            // Kiểm tra xem giá trị có tồn tại trong bảng Follower không
            $followerExists = Follower::where(function ($query) use ($myId, $idFriend) {
                $query->where('my_id', $myId)
                    ->where('id_follower', $idFriend);
            })
                ->orWhere(function ($query) use ($myId, $idFriend) {
                    $query->where('my_id', $idFriend)
                        ->where('id_follower', $myId);
                })
                ->exists();

            // Nếu giá trị không tồn tại trong bảng Follower, thêm vào bảng Friend
            if (!$followerExists && $myId != $idFriend) {
                Friend::create(
                    [
                        'my_id' => $myId,
                        'id_friend' => $idFriend,
                        'status' => Friend::friend,
                    ]
                );
            }
        }


        // Lấy các bản ghi trùng nhau trong bảng Friend
        $duplicateRecords = Friend::select('my_id', 'id_friend')
            ->groupBy('my_id', 'id_friend')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        // Xoá các bản ghi trùng nhau, chỉ giữ lại một bản ghi duy nhất
        foreach ($duplicateRecords as $record) {
            Friend::where('my_id', $record->my_id)
                ->where('id_friend', $record->id_friend)
                ->limit($record->count - 1)
                ->delete();
        }
    }
}
