<?php

include '../src/MyRedis.php';
include '../src/RedisConnect.php';

use Rean\MyRedis;

$r = MyRedis::getInstance('cache');
$r->set('foo', '123');
var_dump($r->get('foo'));

