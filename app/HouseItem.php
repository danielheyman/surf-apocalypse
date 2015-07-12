<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HouseItem extends Model
{
    public function house()
    {
        return $this->belongsTo('App\House');
    }

    public function item()
    {
        return $this->belongsTo('App\Item');
    }
}
