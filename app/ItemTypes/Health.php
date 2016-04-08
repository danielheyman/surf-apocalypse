<?php

namespace App\ItemTypes;

return [
    'users' => ['human', 'zombie'],
    'in_users_table' => true,
    'decimal' => true,
    'max' => 100,
    'attr' => ['max_daily_usage' => 0],
    'find_chance_computed' => function($user, $target) {
        return 5;
        //return $user->computed('combatDamage') / $target->computed('defense');
    },
    'on_attack' => function($user, $target, $ops, $amount) {
        $ops->giveThisItem($target, -$amount * 2);
    },
    'if_human' => [
        'on_change' => function($user, $ops, $value) {
            if($value <= 0) $ops->kill($user);
        },
        'usable_computed' => function($attr) {
            return $attr->max_daily_usage;
        },
    ],
    'if_zombie' => [
        'on_change' => function($user, $ops, $value) {
            if($value >= 100) $ops->revive($user);
        },
    ]
];
