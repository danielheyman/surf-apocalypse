<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Equip extends Model
{
    public function item_type()
    {
        return $this->belongsTo('App\ItemType');
    }
    public function sprite() {
        return $this->item_type()->first(['sprite'])->sprite;
    }
    public function sprite_location() {
        return app("EquipType")->idToLocation($this->sprite());
    }
}
