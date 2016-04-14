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
    private $ops = null;
            
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
    
    public function items($model = false)
    {
        return $this->hasMany('App\Item');
    }
    
    public function equips()
    {
        return $this->hasMany('App\Equip');
    }

    public function itemManager()
    {
        if(!$this->items) $this->items = new \App\Items\ItemManager($this);
        return $this->items;
    }
    
    public function equipManager()
    {
        if(!$this->equips) $this->equips = new \App\Equips\EquipManager($this);
        return $this->equips;
    }
    
    public function ops()
    {
        if(!$this->ops) $this->ops = new \App\UserOps\OpsManager($this);
        return $this->ops;
    }
    
    public function onCreate() {
        if($this->human === null) $this->human = true;
        
        $this->equipManager()->giveSet();
        $this->itemManager()->giveSet();
    }
}
