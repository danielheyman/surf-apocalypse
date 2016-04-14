<?php
namespace App\Items\Type;

abstract class CtpBadges extends \App\Items\Item {    
    public $users = ['human', 'zombie'];
    public $max = 1;
} 

$module['CtpBadge50'] = new class extends CtpBadges {
    public $name = 'CtpBadge50';
    protected $attr = ['link' => ''];
    
    protected $findable = [
        'every' => 50,
    ];
    
    public function onCreate($attr) {
        $attr->link = ''; // generate badge link
    }
};
