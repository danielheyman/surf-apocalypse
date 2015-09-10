<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    protected $fillable = ['name', 'url'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function updateUserSiteCount()
    {
        $user = $this->user;
        $user->website_count = $user->websites()->where('enabled', true)->count();
        $user->save();
    }
}
