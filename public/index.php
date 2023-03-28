<?php

$config = require('../require.php');
$route = require('../config/route.php');

$logger = new \Lightuna\Log\Logger(new \Lightuna\Util\TemplateResolver());
$logStream = match ($config['log']['type']) {
    'file' => new \Lightuna\Stream\File($config['log']['file']),
};
$logger->setStream($logStream);

$router = new \Lightuna\Route\Router($route);

try {
    $app = new \Lightuna\Core\App($config, $logger, $router);
    $httpRequest = new \Lightuna\Http\HttpRequest($_SERVER, $_POST, $_GET);
    $app->run($httpRequest);
} catch (Throwable $e) {
    echo $e->getMessage();
    echo $e->getTraceAsString();
}
