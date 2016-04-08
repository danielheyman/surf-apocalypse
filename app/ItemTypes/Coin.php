<?php

namespace App\ItemTypes;

return [
    'users' => ['human'],
    'in_users_table' => true,
    'decimal' => true,
    'find_chance' => 50,
    'find_range' => [0.01, 2],
    'split_decimal' => ['gold', 'silver']
];
