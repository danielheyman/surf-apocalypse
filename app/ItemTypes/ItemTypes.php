<?php

namespace App\ItemTypes;

class ItemTypes {
    private $sets = [
        'base' => ['health/max_daily_usage' => 30],
        'human' => ['health' => 100],
        'zombie' => ['health' => 0]
    ];
    
    private $original_types = [];
    private $user;
    private $types;
    
    public function __construct($user) {
        $this->original_types = [];
        $module = function($file, $types = null) {
            include ("Types/" . $file . ".php");
            if(!$types) {
                $val = $module;
                $this->original_types[$val->name] = $val;
                return;
            }
            foreach($types as $type) {
                $val = $module[$type];
                $this->original_types[$val->name] = $val;
            }
        };
                     
        $module('Coin');
        $module('Views', ['ViewsToday','ViewsTotal']);
        $module('Health');
        $module('CtpBadges', ['CtpBadge50']);
        
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
        $types[$type[0]]->update($value, $inc, $user, $type[1] ?? null);
    }
        
    
    public function cleanTypes($user) : array {
        $user_type = ($user->human) ? 'human' : 'zombie';

        $new = [];
        foreach($this->original_types as $key => $value) {
            if(!$value->is($user_type)) continue;
            $value = clone $value;
            call_user_func(array($value, 'if_' . $user_type));
            $new[$key] = $value;
        }
        return $new;
    }
    
    public function all() : array {
        return $this->types;
    }
    
    public function find($target) : array {
        $finds = [];
        foreach ($this->types as $key => $value) {
            if (!(method_exists($value, 'find'))) continue;
            $amount = $value->find($this->user, $target);
            if($amount != 0) $finds[$key] = $amount;
        }
        return $finds;
    }
    
    public function getMyItems() : array {
        $types = [];
        foreach ($this->types as $key => $value) {
            $value = $this->types[$key]->getValue($this->user, true);
            if($value != 0) {
                $types[$key] = $value;
            }
        }
        return $types;
    }
}
