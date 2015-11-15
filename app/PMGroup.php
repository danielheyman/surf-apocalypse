<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PMGroup extends Model
{
    protected $table = 'pm_groups';
    protected $timestamps = ['user_last_seen', 'user2_last_seen', 'user_last_message', 'user2_last_message'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function user2()
    {
        return $this->belongsTo('App\User', 'user2_id');
    }
}
