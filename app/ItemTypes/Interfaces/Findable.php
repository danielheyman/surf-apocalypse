<?php
namespace App\ItemTypes\Interfaces;

trait Findable {
    
    public function find($user, $target) : float {
        if(!property_exists($this, 'findable')) return 0;
        
        $options = $this->findable;
        if(array_key_exists('computed', $options) && 
            ($amount = call_user_func($options['computed'], $user, $target))) {
            return $amount;
        }
        
        $found = (array_key_exists('chance', $options) && rand(1, 10000) / 100 <= $options['chance']);
        $found = $found || (array_key_exists('every', $options) && $user->views_today != 0 && $user->views_today % $options['every'] == 0);
        
        if($found) {
            if(array_key_exists('range', $options)) {
                $multiplier = ($options['decimal'] ?? false) ? 100 : 1;
                $amount = rand($options['range'][0] * $multiplier, $options['range'][1] * $multiplier) / $multiplier;
                return $amount;
            }
            return 1;
        }
        return 0;
    }
}
