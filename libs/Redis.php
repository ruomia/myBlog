<?php
namespace libs;
class Redis
{
    public static $redis = null;
    private function __clone(){}
    private function __construct(){}

    // 获取 redis 对象
    public static function getInstance()
    {
        if(self::$redis === null)
        {
            self::$redis = new \Predis\Client([
                'scheme' => 'tcp',
                'host' => '127.0.0.1',
                'port' => 6379,
            ]);
        }
        return self::$redis;
    }
}