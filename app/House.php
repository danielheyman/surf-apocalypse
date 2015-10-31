<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    use OwnedByUserOrTeam;

    protected $fillable = ['owner_id'];

    public function items()
    {
        return $this->hasMany('App\HouseItem');
    }
}
