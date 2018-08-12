<?php

namespace Rean;

/**
 * RedisConnect only for connect to redis
 */
class RedisConnect
{
    /**
     * connect redis config
     * @var $config array
     */
    private static $config = null;

    /**
     * redis instance
     * @var $config array
     */
    private static $redis = null;

    private static $cluster = null;
    private static $hash_key = null;

    /**
     * server's separator in cluster
     */
    private static $conf_separator = '|';

    private static $instance = [];

    /**
     * @param string $cluster cluster's name
     * @param string $hash_key hash value which can find redis server
     * @throws \Exception when cluster is false
     */
    private function __construct($cluster, $hash_key = '')
    {
        if (!$cluster) {
            throw new \Exception('Params cluster must be provided');
        }
        self::$cluster = $cluster;
        self::$hash_key = md5($hash_key);
    }

    /**
     * @param string $cluster cluster's name
     * @param string $hash_key hash value which can find redis server
     * @throws \Exception when cluster is false
     * @return self
     */
    public static function getInstance($cluster, $hash_key = '')
    {
        if (!$cluster) {
            throw new \Exception('Params cluster must be provided');
        }
        $static_key = $hash_key ? md5($cluster.substr(md5($hash_key), 0, 1)) : md5($cluster);
        if (!self::$instance || !isset(self::$instance[$static_key])) {
            self::$instance[$static_key] = new self($cluster, $hash_key);
        }

        return self::$instance[$static_key];
    }

    /**
     * get The instance of redis
     * @throws \Exception when connect redis failed
     * @return object
     */
    public function getRedis()
    {
        $redis_key = self::$hash_key ? md5(self::$cluster.substr(self::$hash_key, 0, 1)) : md5(self::$cluster);
        if (isset(self::$redis[$redis_key])) {
            return self::$redis[$redis_key];
        }
        $conf = self::getConfig();
        if (empty($conf) || !is_array($conf) || !isset($conf['host']) || !isset($conf['port'])) {
            throw new \Exception("The conf is Error");
        }
        try {
            $redis = new \Redis();
            $ret = $redis->connect($conf['host'], $conf['port'], $conf['timeout']);
            if ($ret === false) {
                throw new \Exception($redis->getLastError());
            }
            if (isset($conf['auth'])) {
                $redis->auth($conf['auth']);
            }
            self::$redis[$redis_key] = $redis;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return self::$redis[$redis_key];
    }

    /**
     * get the config to connect redis server
     * @return array
     */
    public static function getConfig()
    {
        if (true || empty(self::$config)) {
            self::$config = require_once(dirname(dirname(__FILE__)).'/conf/redis.php');
            //self::$config = require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/conf/redis.php');
        }
        if (strpos(self::$config[self::$cluster], self::$conf_separator) === false) {
            $redis_node = self::$config[self::$cluster];
        } else {
            $redis_node = self::hashCluster(self::$config[self::$cluster], self::$hash_key);
        }
        return self::$config[$redis_node];
    }

    /**
     * get database server detail info
     * @param string $cluster
     * @param string $hash
     * @return string
     */
    private static function hashCluster($cluster, $hash)
    {
        $node = explode(self::$conf_separator, $cluster);
        $node_num = count($node);
        $hash_num = $hash ? hexdec(substr($hash, 0, 1)) : 0;
        $section = ceil(16 / $node_num);
        $reverse_section = array_reverse(range($node_num * $section - 1, 0, -$section));
        $cluster_num = 0;
        foreach ($reverse_section as $key => $value) {
            if ($key == 0 && $hash_num <= $value) {
                $cluster_num = 0;
                break;
            } elseif ($hash_num <= $reverse_section[$key] && $hash_num > $reverse_section[$key - 1]) {
                $cluster_num = $key;
                break;
            }
        }
        return $node[$cluster_num];
    }

    /**
     * close redis connect
     */
    public function __destruct()
    {
        /**
         * @var $value \Redis
         */
        foreach (self::$redis as $value) {
            if ($value) {
                $value->close();
            }
        }
    }
}
