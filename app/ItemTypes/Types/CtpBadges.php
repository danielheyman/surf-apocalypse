<?php

namespace App\ItemTypes\Types;

$abstract = [
    'users' => ['human', 'zombie'],
    'max' => 1,
];

$module['CtpBadge50'] = array_merge($abstract, [
    'attr' => ['link' => ''],
    'find_every' => 50,
    'on_create' => function($attr) {
        $attr->link = ''; // generate badge link
    }
]);
