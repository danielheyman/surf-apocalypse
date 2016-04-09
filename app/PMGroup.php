<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PMGroup extends Model
{
    protected $table = 'pm_groups';
    public $timestamps = ['user_last_seen', 'user2_last_seen', 'user_last_message', 'user2_last_message'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function user2()
    {
        return $this->belongsTo('App\User', 'user2_id');
    }

    public function pms()
    {
        return $this->hasMany('App\PM', 'pm_group_id');
    }
    
    public static function unreadPm($user_id) {
        $groups = PMGroup::where(function($query) use($user_id) {
            $query->where('user_id', $user_id)->whereRaw('user_last_seen < user2_last_message');
        })->orWhere(function($query) use($user_id) {
            $query->where('user2_id', $user_id)->whereRaw('user2_last_seen < user_last_message');
        })->get(['user_id', 'user2_id']);
        
        $results = [];
        foreach($groups as $group) {
            $results[] = ($group->user_id == $user_id) ? $group->user2_id : $group->user_id;
        }
        return $results;
    }
}
