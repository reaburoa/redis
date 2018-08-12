<?php

return [
    'a' => [
        'host' => '127.0.0.1',
        'port' => 6379,
        'auth' => '***',
        'timeout' => 5
    ],
    'b' => [
        'host' => '127.0.0.1',
        'port' => 6379,
        'auth' => '***',
        'timeout' => 5
    ],
    'cache' => 'a|b',
    'stat' => 'a',
];
