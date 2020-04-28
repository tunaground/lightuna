<?php

use Lightuna\Object\Board;
use Lightuna\Database\ThreadDao;
use Lightuna\Database\DataSource;
use Lightuna\Database\ResponseDao;
use Lightuna\Util\UriParser;
use Lightuna\Util\ContextParser;
use Lightuna\Log\Logger;
use Lightuna\Exception\DataAccessException;
use Lightuna\Exception\InvalidUserInputException;
use Lightuna\Util\ExceptionHandler;

define('FRONT_PAGE', true);

require('./require.php');

$contextParser = new ContextParser();
$logger = new Logger($config['site']['logFilePath'], $contextParser);
$exceptionHandler = new ExceptionHandler($config, $logger);
$dataSource = new DataSource(
    $config['database']['host'],
    $config['database']['port'],
    $config['database']['user'],
    $config['database']['password'],
    $config['database']['schema'],
    $config['database']['options'],
    $logger
);

$conn = $dataSource->getConnection();
$sql = file_get_contents(__DIR__ . '/config/init.sql');
$conn->exec($sql);
?>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>설치</title>
</head>
<body>
</body>
</html>
