<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        'username',
        'password',
        'email',
        'phone_number',
        'fullname',
        'avatar',
        'status',
        'gender',
        'nickname',
        'address',
    ];
    protected $table = 'clients';

    const male = 1;
    const female = 0;
    const order = -1;
}
