<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PM extends Model
{
    protected $table = 'pms';

    public function pm_group()
    {
        return $this->belongsTo('App\PMGroup');
    }
}
