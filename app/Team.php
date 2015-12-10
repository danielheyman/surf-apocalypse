<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['name', 'description', 'user_count', 'user_id'];

    public function users()
    {
        return $this->hasMany('App\User');
    }
}
