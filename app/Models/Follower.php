<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    use HasFactory;
    protected $table = 'followers';
    protected $fillable = [
        'my_id',
        'id_follower',
        'status',
    ];
    const un_friend_request = -1;   //gửi kết bạn nhưng từ chối tương đương chỉ follow
    const friend_request = 0;   // gửi kết bạn, chưa chấp nhận
    // const follow = 1;   // chỉ follow, không kết bạn
}
