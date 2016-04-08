<?php

namespace App\ItemTypes\CtpBadge;

return array_merge(include "CtpBadge.php", [
    'abstract' => false,
    'attr' => ['link' => ''],
    'find_every' => 50,
    'on_create' => function($attr) {
        $attr->link = ''; // generate badge link
    }
]);
