<?php
namespace App\Items\Type;

$module['default'] = new class extends \App\Items\Item {
    public $name = 'coins';
    protected $users = ['human'];
    protected $inUsersTable = true;
    
    protected $findable = [
        'decimal' => true,
        'chance' => 90,
        'range' => [0.01, .3]
    ];
};
