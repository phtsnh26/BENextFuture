<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $table = 'groups';
    protected $fillable = [
        'group_name',
        'cover_image',
        'privacy',
        'display',
        'join_approval',
        'post_approval',
    ];
    const public = 1;
    const private = -1;
    const visible = 2;
    const hidden = -2;

    const turnOnJoin = 1;
    const turnOffJoin = 0;
}
