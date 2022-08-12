<?php

namespace Application\Models;

use Application\Core\Model;

trait RedisTrait
{
    private ?\Redis $redis = null;
    private function setRedisConnection(): void
    {
        $redisInfo = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/RedisInfo.ini', true);
        $this->redis = new \Redis();
        $this->redis->connect($redisInfo['server']['adress'], $redisInfo['server']['port']);
    }
}
