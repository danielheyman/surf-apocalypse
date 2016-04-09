<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ItemManager extends Facade
{
    protected static function getFacadeAccessor() { 
        return 'ItemManager';
    }
}
