<?php
namespace App\ItemTypes\Type;

use \App\ItemTypes\Interfaces\{Item,Findable};

$module = new class extends Item {
    public $name = 'coins';
    protected $users = ['human'];
    protected $inUsersTable = true;
    
    use Findable;
    protected $findable = [
        'decimal' => true,
        'chance' => 90,
        'range' => [0.01, .3]
    ];
};
