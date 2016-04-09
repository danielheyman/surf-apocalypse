<?php

namespace App\ItemTypes\Types;

$module = [
    'users' => ['human', 'zombie'],
    'in_users_table' => true,
    'decimal' => true,
    'attr' => ['max_daily_usage' => 0],
    'if_human' => [
        'on_change' => function($user, $value) {
            if($value <= 0) $user->ops('die');
        },
        'usable_computed' => function($attr) {
            return $attr->max_daily_usage;
        },
    ],
    'if_zombie' => [
        'on_change' => function($user, $value) {
            if($value >= 100) $user->ops('revive');
        },
    ]
];
