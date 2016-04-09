<?php
namespace App\ItemTypes\Type;

use \App\ItemTypes\Item;

abstract class CtpBadges extends Item {    
    public $users = ['human', 'zombie'];
    public $max = 1;
} 

$module['CtpBadge50'] = new class extends CtpBadges {
    public $name = 'CtpBadge50';
    protected $attr = ['link' => ''];
    
    protected $findable = [
        'every' => 50,
    ];
    
    public function on_create($attr) {
        $attr->link = ''; // generate badge link
    }
};
