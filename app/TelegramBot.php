<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TelegramBot extends Model
{
    protected $fillable = [
        'public_key', 'token'
    ];

    protected $table = "telegram_bot";
}
