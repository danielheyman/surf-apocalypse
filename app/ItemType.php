<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

interface ItemTypes
{
    const HOUSE = 0;
    const COIN = 1;
}

class ItemType extends Model
{
    public function items()
    {
        return $this->hasMany('App\Item');
    }
}
