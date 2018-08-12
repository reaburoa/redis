<?php

namespace Rean;

class MyRedis
{
    /**
     * @var $redis \Redis
     */
    private static $redis;

    private static $instance = [];

    /**
     * The name of redis cluster
     */
    private static $channel = null;

    private function __construct($channel)
    {
        self::$channel = $channel;
    }

    /**
     * @param string $cluster cluster's name
     * @throws \Exception when cluster is false
     * @return \Redis
     */
    public static function getInstance($cluster)
    {
        if (!$cluster) {
            throw new \Exception('Params cluster must be provided');
        }
        $static_key = md5($cluster);
        if (!self::$instance || !isset(self::$instance[$static_key])) {
            self::$instance[$static_key] = new self($cluster);
        }

        return self::$instance[$static_key];
    }

    public function __call($fun_name, $arguments)
    {
        if (!$fun_name || empty($arguments)) {
            return false;
        }
        try {
            $key = $arguments[0];
            self::$redis = RedisConnect::getInstance(self::$channel, $key)->getRedis();
            $ret = call_user_func_array([self::$redis, $fun_name], $arguments);
            if ($ret === false) {
                throw new \Exception(self::$redis->getLastError());
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return $ret;
    }
}