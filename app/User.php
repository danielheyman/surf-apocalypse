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
        return Item::ownedBy($this->team ?: $this)->get();
    }
}
