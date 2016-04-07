<?php

return [
    'coin' => [
        'users' => ['human'],
        'decimal' => true,
        'find_chance' => 50,
        'find_range' => [0, 2],
        'split_decimal' => ['gold', 'silver'],
    ],
    'health' => [
        'abstract' => true,
        'default' => 100,
        'decimal' => true,
        'max' => 100,
        'find_chance_computed' => function($user, $target) {
            return $user->computed('combatDamage') / $target->computed('defense');
        },
        'on_attack' => function($user, $target, $amount) {
            $target->giveItem('human_health', -$amount * 2);
        }
    ],
    'human_health' => [
        'implements' => 'health',
        'users' => ['human'],
        'attr' => ['max_daily_usage' => ['int', 30]],
        'on_change' => function($user, $value) {
            if($value <= 0) $user->tasks('die');
        }
    ],
    'zombie_health' => [
        'implements' => 'health',
        'users' => ['zombie'],
        'on_change' => function($user, $value) {
            if($value >= 100) $user->tasks('revive');
        },
        'usable_computed' => function($attr) {
            return $attr->max_daily_usage;
        },
    ],
    'ctp_badge' => [
        'abstract' => true,
        'users' => ['human', 'zombie'],
        'find_every' => 50,
    ],
    'ctp_bage_50' => [
        'implements' => 'ctp_badge',
        'max' => 1,
        'attr' => ['link' => ['str', '']],
        'find_every' => 50,
        'on_create' => function($attr) {
            $attr->link = ''; // generate badge link
        }
    ]
];
