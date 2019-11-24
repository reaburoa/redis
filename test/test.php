<?php

require_once "../src/MyRedis.php";
require_once "../src/RedisConnect.php";

use Rean\MyRedis;

class RedisTest extends MyRedis
{
    public function getRedisConf()
    {
        return [
            'a' => [
                'host' => '127.0.0.1',
                'port' => 6379,
                'auth' => '',
                'timeout' => 5
            ],
            'b' => [
                'host' => '127.0.0.1',
                'port' => 6379,
                'auth' => '',
                'timeout' => 5
            ],
            'cache' => 'a|b',
            'stat' => 'a',
        ];
    }
}

$r = RedisTest::getInstance('cache');
$r->set('foo', '123');
var_dump($r->get('foo'));
var_dump($r->del('foo'));

