<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $table = 'comments';
    protected $fillable = [
        'content',
        'interact',
        'id_tag',
        'id_client',
        'id_replier',
        'id_post',
    ];
}
