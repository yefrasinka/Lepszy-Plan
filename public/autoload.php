<?php

/**
 * Enhanced Autoloader to support both old and new autoloading logic.
 */
class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            // Old autoload logic
            $file = str_replace('App\\', 'src\\', $class);
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $file) . '.php';
            $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . $file;

            if (file_exists($file)) {
                require $file;
                return true;
            }

            // New autoload logic for Models and Service namespaces
            $prefixes = [
                'Model\\' => dirname(__DIR__) . '/Model/',
                'Service\\' => dirname(__DIR__) . '/Service/',
            ];

            foreach ($prefixes as $prefix => $base_dir) {
                if (strncmp($class, $prefix, strlen($prefix)) === 0) {
                    $relative_class = substr($class, strlen($prefix));
                    $new_file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

                    if (file_exists($new_file)) {
                        require $new_file;
                        return true;
                    }
                }
            }

            return false;
        });
    }
}

// Register the autoloader
Autoloader::register();

// Check if the Config class exists as a test
if (class_exists('App\\Service\\Config')) {
    echo "Config class loaded successfully.";
} else {
    echo "Failed to load Config class.";
}

