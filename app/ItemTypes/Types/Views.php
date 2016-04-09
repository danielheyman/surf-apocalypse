<?php
namespace App\ItemTypes\Type;

use \App\ItemTypes\Item;

abstract class Views extends Item {
    public $users = ['human', 'zombie'];
    protected $inUsersTable = true;
    protected $sendUpdates = false;    
} 

$module['ViewsToday'] = new class extends Views {
    public $name = 'ViewsToday';
    
    public function cron() {
        //TODO
    }
};

$module['ViewsTotal'] = new class extends Views {
    public $name = 'ViewsTotal';
};
