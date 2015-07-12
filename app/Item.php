<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use OwnedByUserOrTeam;

    public function itemType()
    {
        return $this->belongsTo('App\ItemType');
    }
}
