<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use OwnedByUserOrTeam;

    protected $fillable = ['count', 'owner_id'];

    public function itemType()
    {
        return $this->belongsTo('App\ItemType');
    }
}
