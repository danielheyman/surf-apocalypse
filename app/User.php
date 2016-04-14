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
    
    private $items = null;
    private $equips = null;
    
    /*public function ops()
    {
        $args = func_get_args();
        dd($args);
        OpsManager::call($some_function, $this, $arg1, $arg2);
        call_user_func_array('mysql_safe_query', $args);
    }*/
        
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


    public function item()
    {
        if(!$this->items) $this->items = new \App\Items\ItemManager($this);
        return $this->items;
    }
    
    public function equip()
    {
        if(!$this->equips) $this->equips = new \App\Equips\EquipManager($this);
        return $this->equips;
    }
    
    public function onCreate() {
        if($this->human === null) $this->human = true;
        
        $this->equip()->giveSet();
        $this->item()->giveSet();
    }
}
