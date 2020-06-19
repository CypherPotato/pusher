<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PushMessage extends Model
{
    protected $fillable = [
        'private_key', 'subject', 'message', 'id'
    ];

    protected $table = "pushmessages";

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
