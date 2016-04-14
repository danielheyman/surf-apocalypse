<?php

namespace App\Items;

class ItemManager {
    private $sets = [
        'base' => ['health/max_daily_usage' => 30],
        'human' => ['health' => 100],
        'zombie' => ['health' => 0]
    ];
    
    private $original_types = [];
    private $user;
    private $types;
    
    public function __construct($user) {
        $this->module('Coin')
            ->module('Views', ['ViewsToday','ViewsTotal'])
            ->module('Health')
            ->module('CtpBadges', ['CtpBadge50']);
        
        $this->user = $user;     
        if($user) $this->types = $this->cleanTypes();
    }
    
    private function module($file, $types = ['default']) {
        include ("Types/" . $file . ".php");
        
        foreach($types as $type) {
            $val = $module[$type];
            $this->original_types[$val->name] = $val;
        }
        
        return $this;
    }
        
    public function giveSet() {
        $set = array_merge($this->sets['base'], $this->sets[$this->user->human ? 'human' : 'zombie']);
        foreach($set as $type => $value) {
            $this->give($type, $value, false);
        }
    }
    
    public function give($type, $value = 1, $inc = true) {
        $original_type = $type;

        $type = explode("/", $type);
        if(!array_key_exists($type[0], $types)) return;
        $types[$type[0]]->update($value, $inc, $this->user, $type[1] ?? null);
    }
        
    
    private function cleanTypes() : array {
        $user_type = ($this->user->human) ? 'human' : 'zombie';

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
