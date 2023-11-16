<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;
    protected $table = "friends";
    protected $fillable = [
        'my_id',
        'id_friend',
        'status',
    ];
    const friend = 1;
    const turn_off_notifications = 0;
    const unfollow = -1;
}
