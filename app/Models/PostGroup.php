<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostGroup extends Model
{
    use HasFactory;
    protected $table = 'post_groups';
    protected $fillable = [
        'caption',
        'images',
        'privacy',
        'status',
        'id_client',
        'id_group',
        'id_tag',
    ];
    const APPROVED = 1;
    const PENDING = 0;
}
