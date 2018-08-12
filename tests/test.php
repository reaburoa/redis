<?php

require '../conf/redis.php';
require '../src/MyRedis.php';
require '../src/RedisConnect.php';

use Rean\MyRedis;

$my_redis = MyRedis::getInstance('cache');
$my_redis->set('qqq', 123);
$my_redis->hSet('hhh', 'f', 1);

var_dump($my_redis->keys('*'));

