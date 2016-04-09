<?php

namespace App\ItemTypes\Types;

$abstract = [
    'users' => ['human', 'zombie'],
    'in_users_table' => true,
    'send_updates' => false 
];

$module['ViewsToday'] = array_merge($abstract, [
    'cron' => 'do cron task'
]);

$module['ViewsTotal'] = $abstract;
