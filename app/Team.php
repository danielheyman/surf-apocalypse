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
        return House::ownedByMe()->first();
    }

    public function items()
    {
        return Item::ownedByMe()->get();
    }

    public function ownedByMe($query)
    {
        $query->ownedBy($this);
    }
}
