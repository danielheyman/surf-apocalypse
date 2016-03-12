<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

interface UsersAllowed
{
    const ZOMBIE = 0;
    const HUMAN = 1;
    const ZOMBIE_AND_HUMAN = 2;
}

interface ItemTypes
{
    const COIN = 0;
    const MATERIAL = 1;
    const EQUIP = 2;
}

//Icon
//  next 3, item number
//  last number, 0 (png) - 1 (jpg)
//Sprite:
//  next 3, folder number
//  optional next 3, inner folder
//  next 3, item number
//  last number, 0 (png) - 1 (jpg)
class ItemType extends Model
{
    public function items()
    {
        return $this->hasMany('App\Item');
    }
    
    public function equips()
    {
        return $this->hasMany('App\Equip');
    }
    
    public function setIconAttribute($value)
    {
        $this->attributes['icon'] = intval($value);
    }
}
