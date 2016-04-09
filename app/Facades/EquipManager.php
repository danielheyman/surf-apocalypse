<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class EquipManager extends Facade
{
    protected static function getFacadeAccessor() { 
        return 'EquipManager';
    }
}
