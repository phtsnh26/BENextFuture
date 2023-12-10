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
    const member = 1;
    const admin = 2;
    const post_moderator = 3;
    const member_moderator = 4;
    const moderator = 5;
}
