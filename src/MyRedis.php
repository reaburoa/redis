<?php

namespace Rean;

abstract class MyRedis
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
            self::$instance[$static_key] = new static($cluster);
        }

        return self::$instance[$static_key];
    }

    abstract public function getRedisConf();

    public function __call($fun_name, $arguments)
    {
        if (!$fun_name || empty($arguments)) {
            return false;
        }
        try {
            $key = $arguments[0];
            self::$redis = RedisConnect::getInstance($this->getRedisConf(), self::$channel, $key)->getRedis();
            $ret = call_user_func_array([self::$redis, $fun_name], $arguments);
            $last_error = self::$redis->getLastError();
            if ($ret === false && $last_error) {
                throw new \Exception($last_error);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return $ret;
    }
}
