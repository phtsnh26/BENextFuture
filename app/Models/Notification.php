<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'notifications';
    protected $fillable = [
        'id_client',
        'my_id',
        'id_group',
        'id_post',
        'type',
    ];
    const request_friend = 1;       //gửi lời mời kết bạn
    const invite_group = 2;         //mời tham gia group
    const grant_role_group = 3;     // cấp quyền của group
    const like_post = 4;
    const comment = 5;
    const like_story = 6;
    const tag_comment = 7;
    const tag_post = 8;

}
