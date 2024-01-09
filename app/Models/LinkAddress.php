<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkAddress extends Model
{
    use HasFactory;
    protected $table = 'link_addresses';
    protected $fillable = [
        'id_client',
        'link',
        'type',
        'icon',
        'name',
    ];
    const YOUTUBE   = 1;
    const THREADS   = 2;
    const FACEBOOK  = 3;
    const INSTAGRAM = 4;
    const X         = 5;
    const TIKTOK    = 6;

    const LINK_FACEBOOK = "fa-brands fa-facebook";
    const LINK_X = "fa-brands fa-x-twitter";
    const LINK_YOUTUBE = "fa-brands fa-youtube";
    const LINK_TIKTOK = "fa-brands fa-tiktok";
    const LINK_INSTAGRAM = "fa-brands fa-instagram";
    const LINK_THREADS = "fa-brands fa-threads";
    public static function checkType($type)
    {
        if ($type == LinkAddress::THREADS) {
            return LinkAddress::LINK_THREADS;
        } elseif ($type == LinkAddress::YOUTUBE) {
            return LinkAddress::LINK_YOUTUBE;
        } elseif ($type == LinkAddress::FACEBOOK) {
            return LinkAddress::LINK_FACEBOOK;
        } elseif ($type == LinkAddress::INSTAGRAM) {
            return LinkAddress::LINK_INSTAGRAM;
        } elseif ($type == LinkAddress::X) {
            return LinkAddress::LINK_X;
        } else {
            return LinkAddress::LINK_TIKTOK;
        }
    }
}
