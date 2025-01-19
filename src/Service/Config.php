<?php

namespace App\Service;

class Config {
    private static ?array $config = null;

    protected static function init() {
        $configFile = __DIR__ . '/../../config/config.php';

        if (!file_exists($configFile)) {
            throw new \Exception("Configuration file not found: $configFile");
        }

        self::$config = include $configFile;
    }

    public static function get($key, $default = null) {
        if (!self::$config) {
            self::init();
        }

        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $segment) {
            if (isset($value[$segment])) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }

        return $value;
    }
}
