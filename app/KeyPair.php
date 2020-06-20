<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KeyPair extends Model
{
    protected $fillable = [
        'private_key', 'public_key', 'text', 'id'
    ];

    protected $table = "keypair";

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
