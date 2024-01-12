<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
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
    const other = -1;

    const account = 1;
    const banned_account = 0;
    
    public static function getFriend($id)
    {
        $result = Friend::select('id_friend as id_client')
            ->where('my_id', $id)
            ->union(
                Friend::select('my_id as id_client')
                    ->where('id_friend', $id)
            )
            ->pluck('id_client')->toArray();
        return $result;
    }

}