<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PM extends Model
{
    protected $table = 'pms';
    protected $fillable = ['message', 'sender', 'event', 'event_data'];

    public function pm_group()
    {
        return $this->belongsTo('App\PMGroup');
    }
}
