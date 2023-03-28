<?php

$config = require('../require.php');
$route = require('../config/route.php');

<<<<<<< HEAD
$logger = new \Lightuna\Log\Logger(new \Lightuna\Util\MessageMaker());
=======
$logger = new \Lightuna\Log\Logger(new \Lightuna\Util\TemplateResolver());
>>>>>>> develop2
$logStream = match ($config['log']['type']) {
    'file' => new \Lightuna\Stream\File($config['log']['file']),
};
$logger->setStream($logStream);

$router = new \Lightuna\Route\Router($route);

<<<<<<< HEAD
if ($config['site']['debug']) {
    $logger->debug('job started');
}

try {
    $app = new \Lightuna\Core\App($config, $logger, $router);
    $app->run();
} catch (Throwable $e) {
    echo $e->getMessage();
}

if ($config['site']['debug']) {
    $logger->debug('job ended');
}
=======
try {
    $app = new \Lightuna\Core\App($config, $logger, $router);
    $httpRequest = new \Lightuna\Http\HttpRequest($_SERVER, $_POST, $_GET);
    $app->run($httpRequest);
} catch (Throwable $e) {
    echo $e->getMessage();
    echo $e->getTraceAsString();
}
>>>>>>> develop2
