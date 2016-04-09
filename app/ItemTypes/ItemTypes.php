<?php

namespace App\ItemTypes;

class ItemTypes {
    private $sets = [
        'base' => ['health/max_daily_usage' => 30],
        'human' => ['health' => 100],
        'zombie' => ['health' => 0]
    ];
    
    private $original_types;
    private $user;
    private $types;
    
    public function __construct($user) {
        $include = function($file, $type = null) {
            include ("Types/" . $file . ".php");
            if($type) return $module[$type];
            return $module;
        };
             
        $this->original_types = [
            'coins' => $include('Coin'),
            'views_today' => $include('Views', 'ViewsToday'),
            'views_total' => $include('Views', 'ViewsTotal'),
            'health' => $include('Health'),
            'ctp_badge_50' => $include('CtpBadges', 'CtpBadge50')
        ];
        
        if($user) $this->types = $this->cleanTypes($user);
        $this->user = $user;     
    }
        
    public function giveSet($user) {
        $user = $user ?: $this->user;
        
        $set = array_merge($this->sets['base'], $this->sets[$user->human ? 'human' : 'zombie']);
        foreach($set as $type => $value) {
            $this->giveItem($type, $value, false, $user);
        }
    }
    
    public function giveItem($type, $value = 1, $inc = true, $user = null) {
        $original_type = $type;
        $types = $user ? $this->cleanTypes($user) : $this->types;
        $user = $user ?: $this->user;
        
        $type = explode("/", $type);
        if(!array_key_exists($type[0], $types)) return;

        $max = null;
        if(count($type) === 1 && property_exists($types[$type[0]], 'max')) {
            $max = $types[$type[0]]->max;
            if($value > $max) $value = $max;
        } else if(!$inc && $value < 0)
            $value = 0;
        
        // If in users table
        if($this->inUsersTable($types, $type[0])) {
            $key = implode("_", $type);
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
            $this->notifyItemUpdate($original_type, $user);
            return;
        }
        
        // If in items table
        $key = $type[0];
        
        // If item exists
        if($item = $user->items()->where('item_type', $key)->first()) {
            if(count($type) == 2) {
                $attr = array_merge($item->attributes, [$type[1] => $value]);
                $item->attributes = $attr;
                $item->save();
            }
            else if($inc) {
                $item->increment('value', $amount);
                if($max && $user->{$key} > $max) {
                    $user->{$key} = $max;
                    $user->save();
                } else if($user->{$key} < 0) {
                    $user->{$key} = 0;
                    $user->save();
                }
            }
            else {
                $item->value = $value;
                $item->save();
            }
            $this->notifyItemUpdate($original_type, $user);
            return;
        } 
        
        // If item does not exist
        if($value < 0) $value == 0;
        $attr = [];
        if(property_exists($types[$key], 'attr')) {
            foreach($types[$key]->attr as $k => $val) {
                $attr[$k] = $val; 
            }
        }
        if(count($type) == 2) {
            $attr[$type[1]] = $value;
            $value = 0;
        }
        $user->items()->create([
            'item_type' => $key,
            'count' => $value,
            'attributes' => $attr
        ]);
        $this->notifyItemUpdate($original_type, $user);
    }
    
    public function notifyItemUpdate($type, $user) {
        $types = $user ? $this->cleanTypes($user) : $this->types;
        
        if(property_exists($types[$type], 'send_updates') && $types[$type]->send_updates == false) return;
        
        event(new \App\Events\UpdatedItem($type, $user, $this->getItemValue($type, $user)));  
    }
    
    public function getItemValue($type, $user = null) {
        $user = $user ?: $this->user;
        $types = $user ? $this->cleanTypes($user) : $this->types;
        
        if($this->inUsersTable($types, $type)) {
            return $user->{$type};
        } else if($item = $user->items()->where('item_type', $type)->first()) {
            return $item->value;
        }
        
        return 0;
    }
    
    public function inUsersTable($types, $type) {
        return (property_exists($types[$type], 'in_users_table') && $types[$type]->in_users_table);
    }
    
    public function cleanTypes($user) {
        $new = [];
        foreach($this->original_types as $key => $value) {
            $cleaned = $this->cleanItemType($key, $value, $user);
            if($cleaned !== null) $new[$key] = $cleaned;
        }
        return $new;
    }
        
    public function cleanItemType($key, $value, $user) {
        $user_type = ($user->human) ? 'human' : 'zombie';

        if(!in_array($user_type, $value['users'])) {
            return null;
        }
        if(array_key_exists('abstract', $value)) {
            if($value['abstract']) return null;
            unset($value['abstract']);
        }
        if(array_key_exists('if_' . $user_type, $value)) {
            $value = array_merge($value, $value['if_' . $user_type]);
        }
        unset($value['if_human']);
        unset($value['if_zombie']);
        unset($value['users']);
        return (object) $value;
    }
    
    public function all() {
        return $this->types;
    }
            
    /*public function onAttack($target, $items) {
        foreach($this->types as $key => $value) {
            if(!property_exists($value, 'on_attack')) continue;
            
            $amount = array_key_exists($key, $items) ? $items[$key] : 0;
            call_user_func($this->types[$item]->on_attack, $user, $target, $amount);
        }
    }*/
    
    public function find($target) {
        $finds = [];
        foreach ($this->types as $key => $value) {
            if(property_exists($value, 'find_chance_computed') && 
                ($amount = call_user_func($value->find_chance_computed, $this->user, $target))) {
                $finds[$key] = $amount;
                continue;
            }
            
            $found = (property_exists($value, 'find_chance') && rand(1, 10000) / 100 <= $value->find_chance);
            $found = $found || (property_exists($value, 'find_every') && $this->user->views_today != 0 && $this->user->views_today % $value->find_every == 0);
            
            if($found) {
                if(property_exists($value, 'find_range')) {
                    $multiplier = (property_exists($value, 'decimal') && $value->decimal) ? 100 : 1;
                    $amount = rand($value->find_range[0] * $multiplier, $value->find_range[1] * $multiplier) / $multiplier;
                    $finds[$key] = $amount;
                }
                else $finds[$key] = 1;
            }
        }
        return $finds;
    }
    
    public function getMyItems() {
        $types = [];
        foreach ($this->types as $key => $value) {
            if(property_exists($value, 'send_updates') && $value->send_updates == false) continue;
            
            $value = $this->getItemValue($key);
            if($value != 0) {
                $types[$key] = $value;
            }
        }
        return $types;
    }
}
