<?php
// Version
$version = '0.4.2';

// Check Front Page
if (FRONT_PAGE !== true) {
    header('HTTP/1.0 403 Forbidden');
}

session_start();


// Set Autoloader
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

// Initialize Configuration
$lightuna_env = getenv('LIGHTUNA_ENV');
if ($lightuna_env === false) {
    $config = require(__DIR__ . "/config/profile.php");
} else {
    $config = require(__DIR__ . "/config/profile.{$lightuna_env}.php");
}

// Set Global Exception Handler
use Lightuna\Util\ContextParser;
use Lightuna\Log\Logger;
use Lightuna\Util\ExceptionHandler;

$contextParser = new ContextParser();
$logger = new Logger($config['site']['logFilePath'], $contextParser);
$exceptionHandler = new ExceptionHandler($config, $logger);
set_exception_handler([$exceptionHandler, 'global']);

// Execute Middleware
use Lightuna\Common\MiddlewareInterface;

foreach($config['middleware'] as $middleware) {
    /**
     * @var MiddlewareInterface $middleware
     */
    if ($middleware->isEnabled() === true) {
        $middleware->handle($config, $_SERVER);
    }
}
