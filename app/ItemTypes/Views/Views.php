<?php

namespace App\ItemTypes\Views;

// Abstract

return [
    'abstract' => true,
    'users' => ['human', 'zombie'],
    'in_users_table' => true,
    'on_attack' => function($user, $target, $ops, $amount) {
        $ops->giveThisItem($user, 1);
    }
];
