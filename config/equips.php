<?php

return [
    'priority' => ['body', 'feet', 'legs', 'hair', 'head', 'torso'],
    'sets' => [
        'base' => ['body/light', 'feet/brown_shoes', 'hair/plain_blonde', 'head/leather_cap', 'legs/teal_pants', 'torso/brown_longsleeve']
    ],
    'body' => [
        'light' => []
    ],
    'feet' => [
        'brown_shoes' => [
            'speed' => 30
        ]
    ],
    'hair' => [
        'plain_blonde' => []
    ],
    'head' => [
        'leather_cap' => [
            'defense' => 10
        ]
    ],
    'legs' => [
        'teal_pants' => [
            'defense' => 10
        ]
    ],
    'torso' => [
        'brown_longsleeve' => [
            'defense' => 10
        ]
    ],
];
