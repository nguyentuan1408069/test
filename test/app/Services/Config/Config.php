<?php

namespace App\Services\Config;

class Config
{
    protected static $config = [];

    public static function loadConfigs()
    {
        if (0 === count(self::$config)) {
            static::$config = require __DIR__.'/../../../config.php';
        }
    }

    public static function get(string $key)
    {
        self::loadConfigs();

        return array_get(self::$config, $key);
    }
}