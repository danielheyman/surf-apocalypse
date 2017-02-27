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
    
    private $itemManager = null;
    private $equipManager = null;
    private $opManager = null;
    
    public function isOwnerOfTeam() {
        return $this->team && $this->team->isOwnedBy($user);
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
        if(!$this->itemManager) $this->itemManager = new \App\Items\ItemManager($this);
        return $this->itemManager;
    }
    
    public function equipManager()
    {
        if(!$this->equipManager) $this->equipManager = new \App\Equips\EquipManager($this);
        return $this->equipManager;
    }
    
    public function opManager()
    {
        if(!$this->opManager) $this->opManager = new \App\UserOps\OpManager($this);
        return $this->opManager;
    }
    
    public function onCreate() {
        if($this->human === null) {
            $this->human = true;
        }
        
        $this->equipManager()->giveSet();
        $this->itemManager()->giveSet();
    }
}
