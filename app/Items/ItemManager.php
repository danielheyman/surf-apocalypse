<?php

namespace App\Items;

class ItemManager {
    private $sets = [
        'base' => ['health/max_daily_usage' => 30],
        'human' => ['health' => 100],
        'zombie' => ['health' => 0]
    ];
    
    private $user;
    private $types;
    
    public function __construct($user) {
        $this->user = $user;

        $this->module('Coin')
            ->module('Views', ['ViewsToday','ViewsTotal'])
            ->module('Health')
            ->module('CtpBadges', ['CtpBadge50']);    
    }
    
    private function module($file, $types = null) {
        if(!$types) $types = [$file];
        require_once("Types\\{$file}.php");    
            
        foreach($types as $type) {
            $namespace = "App\Items\Types\\{$type}";
        
            try {
                $package = new $namespace($this->user); 
                $this->types[$package->name] = $package;
            } catch (ItemNotUsableException $e) {}
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
        $type = explode("/", $type);
        if(!array_key_exists($type[0], $this->types)) return;
        $this->types[$type[0]]->update($value, $inc, $this->user, $type[1] ?? null);
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
