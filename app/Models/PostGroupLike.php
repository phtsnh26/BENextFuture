<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostGroupLike extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_post',
        'id_client'
    ];
    protected $table = 'post_group_likes';
}
