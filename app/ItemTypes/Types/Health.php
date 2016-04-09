<?php
namespace App\ItemTypes\Type;

use \App\ItemTypes\Item;

$module['default'] = new class extends Item {
    public $name = 'health';
    protected $users = ['human', 'zombie'];
    protected $inUsersTable = true;
    protected $attr = ['max_daily_usage' => 0];
    
    protected $findable = [
        'decimal' => true,
    ];
    
    public function ifHuman() {
        $this->onChange = function($user) {
            if($this->getValue() <= 0) $user->ops('die');
        };
        $this->findable['computed'] = function($attr) {
            return $attr->max_daily_usage;
        };
    }
    
    public function ifZombie() {
        $this->onChange = function($user) {
            if($this->getValue() >= 100) $user->ops('revive');
        };
    }
};
