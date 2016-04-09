<?php
namespace App\ItemTypes\Type;

use \App\ItemTypes\Interfaces\{Item,Findable};

abstract class Views extends Item {
    public $users = ['human', 'zombie'];
    protected $inUsersTable = true;
    protected $sendUpdates = false;
    
    use Findable;
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
