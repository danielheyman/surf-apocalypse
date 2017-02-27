<?php
namespace App\Items\Types;

abstract class Views extends \App\Items\Item {
    protected $users = ['human', 'zombie'];
    protected $inUsersTable = true;
    protected $sendUpdates = false;    
} 

class ViewsToday extends Views {
    public $name = 'ViewsToday';
    
    public function cron() {
        //TODO
    }
}

class ViewsTotal extends Views {
    public $name = 'ViewsTotal';
}
