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

    public function giveItem($item_type, $count)
    {
        if (is_string($item_type) && !($type = ItemType::where('id', $item_type)->first(['id', 'item_type']))) {
            return;
        } else {
            $type = $item_type;
            $item_type = $type->id;
        }

        if ($type->item_type == ItemTypes::COIN) {
            $this->increment('coins', $change);
        } else {
            if ($update = $this->items()->where('item_type_id', $item_type)->first(['id'])) {
                $update->increment('count', $count);
            } else {
                $type->items()->create([
                    'count' => $count,
                    'user_id' => $this->id,
                ]);
            }
        }
    }
    
    public function giveEquip($equip) {
        $equip->equips()->create([
            'user_id' => $this->id
        ]);
    }
    
    public function onCreate() {
        $this->giveEquip(app("EquipType")->nameToItemType("torso/shirts/brown_longsleeve"));
        $this->giveEquip(app("EquipType")->nameToItemType("head/caps/leather_cap"));
        $this->giveEquip(app("EquipType")->nameToItemType("hair/plain/blonde"));
        $this->giveEquip(app("EquipType")->nameToItemType("legs/pants/teal_pants"));
        $this->giveEquip(app("EquipType")->nameToItemType("feet/shoes/brown_shoes"));
        $this->giveEquip(app("EquipType")->nameToItemType("body/light"));

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
