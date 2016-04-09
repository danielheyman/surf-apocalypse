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
    
    public function ops()
    {
        $args = func_get_args();
        dd($args);
        OpsManager::call($some_function, $this, $arg1, $arg2);
        call_user_func_array('mysql_safe_query', $args);
    }
        
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
        return \App\Facades\EquipManager::priorityString($this->equips);
    }
    
    public function onCreate() {
        if($this->human === null) $this->human = true;
        \App\Facades\EquipManager::giveSet($this);
        \App\Facades\ItemManager::giveSet($this);
    }
}
