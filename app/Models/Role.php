<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table = 'roles';
    protected $fillable = [
        'role_name',
    ];
    const newbie = 1;
    const admin = 2;
    const post_moderation = 3;
    const member_moderation = 4;
}
