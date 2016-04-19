<?php
namespace App\Items;

abstract class Item {
    
    protected $user;
    
    public function __construct($user) {
        $this->user = $user;
        
        $user_type = $this->user->human ? 'human' : 'zombie';
        
        if(!in_array($user_type, $this->users)) {
            throw new ItemNotUsableException();
        }
        
        call_user_func(array($this, 'if' . ucfirst($user_type)));
    }
    
    public function update($value, $inc, $attribute_to_update) {
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
                $this->user->increment($key, $value);
                if($max && $this->user->{$key} > $max) {
                    $this->user->{$key} = $max;
                    $this->user->save();
                } else if($this->user->{$key} < 0) {
                    $this->user->{$key} = 0;
                    $this->user->save();
                }
            }
            else {
                $this->user->{$key} = $value;
                $this->user->save();
            }
            if(!$attribute_to_update) $this->notifyUpdate();
            return;
        }
        
        // If item exists
        if($item = $this->user->items()->where('item_type', $this->name)->first()) {
            if($attribute_to_update) {
                $attr = array_merge($item->attributes, [$attribute_to_update => $value]);
                $item->attributes = $attr;
                $item->save();
            }
            else if($inc) {
                $item->increment('value', $amount);
                if($max && $this->user->{$this->name} > $max) {
                    $this->user->{$this->name} = $max;
                    $this->user->save();
                } else if($this->user->{$this->name} < 0) {
                    $this->user->{$this->name} = 0;
                    $this->user->save();
                }
            }
            else {
                $item->value = $value;
                $item->save();
            }
            if(!$attribute_to_update) $this->notifyUpdate();
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
        $item = $this->user->items()->create([
            'item_type' => $this->name,
            'count' => $value,
            'attributes' => $attr
        ]);
        $this->notifyUpdate();
    }
    
    public function notifyUpdate() {
        if(method_exists($this, 'onChange')) {
            $this->onChange($this->user);
        }  
        if($this->sendUpdates ?? true) {
            event(new \App\Events\UpdatedItem($this->name, $this->user, $this->getValue()));
        }
    }
    
    public function getValue($updatesOnly = false) : float {
        if($updatesOnly && !($this->sendUpdates ?? true)) {
            return 0;
        } else if($this->inUsersTable ?? false) {
            return $this->user->{$this->name};
        } else if($item = $this->user->items()->where('item_type', $this->name)->first()) {
            return $item->value;
        }
        
        return 0;
    }
    
    protected function ifHuman() {
        
    }
    
    protected function ifZombie() {
        
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
