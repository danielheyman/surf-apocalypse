<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['name', 'description', 'user_count', 'user_id'];
    
    public function isOwnedBy($user) {
        return $user->id == $this->owner_id;
    }
    
    public function isMember($user) {
        return $user->team_id == $this->id;
    }

    public function users()
    {
        return $this->hasMany('App\User');
    }
}
