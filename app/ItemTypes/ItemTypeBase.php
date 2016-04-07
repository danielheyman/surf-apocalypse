<?php

namespace App\ItemTypes;


class ItemTypeBase {
    const HUMAN_FINDABLE = true;
    const ZOMBIE_FINDABLE = true;
    const FIND_CHANCE = 0;
    const FIND_DECIMAL = false;
    const FIND_MIN = 0;
    const FIND_MAX = 0;
    const NAME = '';
    
    public static function findableByUserType($user) {
        if($user->human && !static::HUMAN_FINDABLE) return false;
        if(!$user->human && !static::ZOMBIE_FINDABLE) return false;
        return true;
    }
    
    public static function find($user) {
        if(!static::findableByUserType($user)) return 0;
        if(rand(1, 10000) / 100 > static::FIND_CHANCE) return 0;
        
        $decimal = static::FIND_DECIMAL ? 100 : 1;
        return rand(static::FIND_MIN * $decimal, static::FIND_MAX * $decimal) / $decimal;
    }
}
