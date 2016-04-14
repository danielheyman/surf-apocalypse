<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['count', 'user_id', 'item_type', 'attributes'];
    protected $casts = [
        'attributes' => 'json'
    ];

}
