<?php
namespace App\Items;

abstract class Item {
    
    public function update($value, $inc, $user, $attribute_to_update) {
        $max = null;
        if(!$attribute_to_update && property_exists($this, 'max')) {
            $max = $this->max;
            if($value > $max) $value = $max;
        } else if(!$inc && $value < 0) {
            $value = 0;
        }
        
        // If in users table
        if($this->inUsersTable ?? false) {
            $key = $this->name . ($attribute_to_update ? '_' . $attribute_to_update : '');
            if($inc) {
                $user->increment($key, $value);
                if($max && $user->{$key} > $max) {
                    $user->{$key} = $max;
                    $user->save();
                } else if($user->{$key} < 0) {
                    $user->{$key} = 0;
                    $user->save();
                }
            }
            else {
                $user->{$key} = $value;
                $user->save();
            }
            if(!$attribute_to_update) $this->notifyUpdate($user);
            return;
        }
        
        // If item exists
        if($item = $user->items()->where('item_type', $this->name)->first()) {
            if($attribute_to_update) {
                $attr = array_merge($item->attributes, [$attribute_to_update => $value]);
                $item->attributes = $attr;
                $item->save();
            }
            else if($inc) {
                $item->increment('value', $amount);
                if($max && $user->{$this->name} > $max) {
                    $user->{$this->name} = $max;
                    $user->save();
                } else if($user->{$this->name} < 0) {
                    $user->{$this->name} = 0;
                    $user->save();
                }
            }
            else {
                $item->value = $value;
                $item->save();
            }
            if(!$attribute_to_update) $this->notifyUpdate($user);
            return;
        } 
        
        // If item does not exist
        if($value < 0) $value == 0;
        $attr = [];
        if(property_exists($this, 'attr')) {
            foreach($this->attr as $k => $val) {
                $attr[$k] = $val; 
            }
        }
        if($attribute_to_update) {
            $attr[$attribute_to_update] = $value;
            $value = 0;
        }
        if(method_exists($this, 'onCreate')) {
            $this->onCreate($attr);
        }
        $item = $user->items()->create([
            'item_type' => $this->name,
            'count' => $value,
            'attributes' => $attr
        ]);
        $this->notifyUpdate($user);
    }
    
    public function notifyUpdate($user) {
        if(method_exists($this, 'onChange')) {
            $this->onChange($user);
        }  
        if($this->sendUpdates ?? true) {
            event(new \App\Events\UpdatedItem($this->name, $user, $this->getValue($user)));
        }
    }
    
    public function getValue($user, $updatesOnly = false) : float {
        if($updatesOnly && !($this->sendUpdates ?? true)) {
            return 0;
        } else if($this->inUsersTable ?? false) {
            return $user->{$this->name};
        } else if($item = $user->items()->where('item_type', $this->name)->first()) {
            return $item->value;
        }
        
        return 0;
    }
    
    public function availableForUserType($user_type) {
        return in_array($user_type, $this->users);
    }
    
    public function ifHuman() {
        
    }
    
    public function ifZombie() {
        
    }
    
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
