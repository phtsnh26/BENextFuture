<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
    use HasFactory;
    protected $fillable = [
        'username',
        'password',
        'email',
        'phone_number',
        'fullname',
        'date_of_birth',
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
    const account = 1;
    const lock_account = -1;
}
