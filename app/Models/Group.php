<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';
    protected $fillable = [
        'group_name',
        'cover_image',
        'privacy',
        'display',
        'join_approval',
        'post_approval',
        'anonymity',
        'created_at',
    ];
    const public = 1;
    const private = -1;
    const visible = 2;
    const hidden = -2;
    const requiredGroupApproval = 1;   // phải được duyệt join_approval
    const notRequiredGroupApproval = 0;  // không cần duyệt join_approval
    const requiredPostInGroupApproval = 1;   // phải được duyệt post_approval
    const notRequiredPostInGroupApproval = 0;  // không cần duyệt post_approval
}
