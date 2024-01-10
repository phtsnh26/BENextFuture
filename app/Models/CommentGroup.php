<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentGroup extends Model
{
    use HasFactory;
    protected $table = 'comment_groups';
    protected $fillable = [
        'content',
        'id_tag',
        'id_client',
        'id_replier',
        'id_post',
    ];
}
