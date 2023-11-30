<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $table = 'groups';
    protected $fillable = [
        'name_group',
        'cover_image',
        'privacy',
        'display',
    ];
    const public = 1;
    const private = -1;
    const display_group = 2;
    const hidden_group = -2;
}
