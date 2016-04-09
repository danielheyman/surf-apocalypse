<?php
namespace App\ItemTypes\Type;

use \App\ItemTypes\Item;

$module['default'] = new class extends Item {
    public $name = 'coins';
    protected $users = ['human'];
    protected $inUsersTable = true;
    
    protected $findable = [
        'decimal' => true,
        'chance' => 90,
        'range' => [0.01, .3]
    ];
};
