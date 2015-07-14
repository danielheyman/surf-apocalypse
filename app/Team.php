<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    public function users()
    {
        return $this->hasMany('App\User');
    }

    public function house()
    {
        return House::ownedBy($this)->first();
    }

    public function items()
    {
        return Item::ownedBy($this)->get();
    }
}
