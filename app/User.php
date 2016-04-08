<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    protected $fillable = ['name', 'email', 'password', 'confirmation_code', 'human'];
    
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }
        
    public function team()
    {
        return $this->belongsTo('App\Team');
    }

    public function websites()
    {
        return $this->hasMany('App\Website');
    }

    public function items()
    {
        return $this->hasMany('App\Item');
    }
    
    public function equips()
    {
        return $this->hasMany('App\Equip');
    }
    
    public function orderedEquipsString()
    {
        return \App\Facades\EquipTypes::priorityString($this->equips);
    }
    
    public function onCreate() {
        if($this->human === null) $this->human = true;
        \App\Facades\EquipTypes::giveSet($this);
        \App\Facades\ItemTypes::giveSet($this);
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
            event(new \App\Events\UpdatedCoins($this));
        }
    }
}
