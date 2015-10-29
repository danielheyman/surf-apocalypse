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

    public function syncOriginal()
    {
        $this->syncCoins();

        parent::syncOriginal();
    }

    public function syncOriginalAttribute($attribute)
    {
        if ($attribute == 'coins') {
            $this->syncCoins();
        }

        parent::syncOriginalAttribute($attribute);
    }

    public function syncCoins()
    {
        if (!$this->getOriginal('coins')) {
            return;
        }

        if ($this->original['coins'] != $this->coins) {
            foreach($this->users as $user) {
                $user->coins = $this->coins;
                $user->save();
            }
        }
    }
}
