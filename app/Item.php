<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['count', 'user_id'];

    public function itemType()
    {
        return $this->belongsTo('App\ItemType');
    }
}
