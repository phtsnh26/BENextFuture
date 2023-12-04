<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestGroup extends Model
{
    use HasFactory;
    protected $table = 'request_groups';
    protected $fillable = [
        'id_client',
        'id_group',
        'id_invite',
        'status',
    ];
    const come = 1;
    const invite = 0;
}
