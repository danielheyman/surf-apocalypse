<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

interface ItemTypes
{
    const COIN = 0;
    const MATERIAL = 1;
}

//Icon
//  first number, 0 (png) - 1 (jpg)
//  next 2, item number
//Sprite:
//  first number, 0 (png) - 1 (jpg)
//  next 2, folder number
//  next 2, item number
interface CharacterTypes
{
    const ZOMBIE = 0;
    const HUMAN = 1;
    const ZOMBIE_AND_HUMAN = 2;
}

class ItemType extends Model
{
    public function items()
    {
        return $this->hasMany('App\Item');
    }
}
