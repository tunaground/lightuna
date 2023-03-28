<?php

const LIGHTUNA_VERSION = "0.7.0";

session_start();

spl_autoload_register(function ($class) {
    $prefix = 'Lightuna\\';
    $baseDir = __DIR__ . '/src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

$lightuna_env = getenv('LIGHTUNA_ENV');
if ($lightuna_env === false) {
    $config = require(__DIR__ . "/config/profile.php");
} else {
    $config = require(__DIR__ . "/config/profile.{$lightuna_env}.php");
}

return $config;
