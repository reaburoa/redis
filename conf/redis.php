<?php

return [
    'a' => [
        'host' => '112.74.107.106',
        'port' => 6379,
        'auth' => 'reaburoa_redis',
        'timeout' => 5
    ],
    'b' => [
        'host' => '112.74.107.106',
        'port' => 6379,
        'auth' => 'reaburoa_redis',
        'timeout' => 5
    ],
    'cache' => 'a|b',
    'stat' => 'a',
];
