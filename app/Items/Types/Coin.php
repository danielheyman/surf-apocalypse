<?php
namespace App\Items\Types;

class Coin extends \App\Items\Item {
    public $name = 'coins';
    protected $users = ['human'];
    protected $inUsersTable = true;
    
    protected $findable = [
        'decimal' => true,
        'chance' => 90,
        'range' => [0.01, .3]
    ];
}
