<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $table = 'posts';
    protected $fillable = [
        'caption',
        'images',
        'react',
        'is_view_like',
        'is_view_comment',
        'id_client',
        'id_tag',
        'privacy'
    ];
    const public = 1;
    const friend = 2;
    const private = 4;
}
