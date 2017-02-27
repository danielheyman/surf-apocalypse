<?php
namespace App\Items\Types;

class Health extends \App\Items\Item {
    public $name = 'health';
    protected $users = ['human', 'zombie'];
    protected $inUsersTable = true;
    protected $attr = ['max_daily_usage' => 0];
    
    protected $findable = [
        'decimal' => true,
    ];
    
    protected function ifHuman() {
        $this->onChange = function($user) {
            if($this->getValue() <= 0) $user->ops('die');
        };
        $this->findable['computed'] = function($attr) {
            return $attr->max_daily_usage;
        };
    }
    
    protected function ifZombie() {
        $this->onChange = function($user) {
            if($this->getValue() >= 100) $user->ops('revive');
        };
    }
}
