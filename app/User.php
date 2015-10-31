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

    public function house()
    {
        return House::ownedBy($this->team ?: $this)->first();
    }

    public function items()
    {
        return Item::ownedBy($this->team ?: $this);
    }

    public function createHouse()
    {
        if($this->house())
            return;

        return House::create([
            'owner_id' => $this->team ?: $this
        ]);
    }

    public function leaveTeam()
    {
        $this->team()->dissociate();
        $this->coins = 0;
        $this->save();

        $this->createHouse();
    }

    public function onDelete()
    {
        if(!$this->team) {
            $this->team->delete();
            return;
        }

        $this->house()->delete();
    }

    public function giveCoins($change)
    {
        $team = $this->team;

        if ($team) {
            $team->increment('coins', $change);
        } else {
            $this->increment('coins', $change);
        }
    }

    public function giveItem($item_type, $count)
    {
        if (!($type = ItemType::where('id', $item_type)->first(['id', 'item_type']))) {
            return;
        }

        if ($type->item_type == ItemTypes::COIN) {
            $this->giveCoins($count);
        } else {
            if ($update = $this->items()->where('item_type_id', $item_type)->first(['id'])) {
                $update->increment('count', $count);
            } else {
                $type->items()->create([
                    'count' => $count,
                    'owner_id' => $this->team ?: $this,
                ]);
            }
        }
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
