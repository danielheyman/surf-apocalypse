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
        $this->module('Coin');
        $this->module('Views', ['ViewsToday','ViewsTotal']);
        $this->module('Health');
        $this->module('CtpBadges', ['CtpBadge50']);
        
        if($user) $this->types = $this->cleanTypes($user);
        $this->user = $user;     
    }
    
    public function module($file, $types = ['default']) {
        include ("Types/" . $file . ".php");
        
        foreach($types as $type) {
            $val = $module[$type];
            $this->original_types[$val->name] = $val;
        }
    }
        
    public function giveSet($user = null) {
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
            call_user_func(array($value, 'if' . ucfirst($user_type)));
            $new[$key] = $value;
        }
        return $new;
    }
    
    public function all() : array {
        return $this->types;
    }
    
    public function find($target) : array {
        return $this->findWhereNotZero(function($type) use ($target) {
            return $type->find($this->user, $target);
        });
    }
    
    public function getMyItems() : array {
        return $this->findWhereNotZero(function($type) {
            return $type->getValue($this->user, true);
        });
    }
    
    private function findWhereNotZero($closure) : array {
        $finds = [];
        foreach($this->types as $key => $value) {
            if(($amount = $closure($value)) != 0) 
                $finds[$key] = $amount;
        }
        return $finds;
    }
}
