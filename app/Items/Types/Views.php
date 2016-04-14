<?php
namespace App\Items\Type;

abstract class Views extends \App\Items\Item {
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
