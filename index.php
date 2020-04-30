<?php

use Lightuna\Database\DataSource;
use Lightuna\Database\MariadbThreadDao;
use Lightuna\Database\MariadbResponseDao;
use Lightuna\Database\MysqlThreadDao;
use Lightuna\Database\MysqlResponseDao;
use Lightuna\Object\Board;
use Lightuna\Util\UriParser;
use Lightuna\Util\ContextParser;
use Lightuna\Log\Logger;
use Lightuna\Exception\DataAccessException;
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
$uriParser = new UriParser($config, $_SERVER['REQUEST_URI']);
try {
    $board = new Board($config, $uriParser->getBoardUid());
} catch (UnexpectedValueException $e) {
    $logger->debug('index.php: Invalid Board UID: {msg}', ['msg' => $e->getMessage()]);
    $exceptionHandler->handle('/invalid-board-uid', $e);
}

if ($config['database']['type'] === 'mysql') {
    $threadDao = new MysqlThreadDao($dataSource, $logger);
    $responseDao = new MysqlResponseDao($dataSource, $logger);
} else {
    $threadDao = new MariadbThreadDao($dataSource, $logger);
    $responseDao = new MariadbResponseDao($dataSource, $logger);
}

try {
    $threads = $threadDao->getThreadListByBoardUid($board['id'], $board['maxThreadListView']);
    for ($i = 0; $i < sizeof($threads); $i++) {
        $threads[$i]->setSize($threadDao->getThreadSize($threads[$i]->getThreadUid()) - 1);
        $threads[$i]->setSequence($i + 1);
    }

    $threadViewCount = (count($threads) < $board['maxThreadView']) ? count($threads) : $board['maxThreadView'];
    for ($i = 0; $i < $threadViewCount; $i++) {
        $responseStart = max(0, $threads[$i]->getSize() - $board['maxResponseView']);
        $responseLimit = $board['maxResponseView'];
        if ($responseStart > 0) {
            $threads[$i]->setResponses(array_merge(
                $responseDao->getResponseListByThreadUid(
                    $threads[$i]->getThreadUid(),
                    0,
                    1
                ),
                $responseDao->getResponseListByThreadUid(
                    $threads[$i]->getThreadUid(),
                    $responseStart,
                    $responseLimit
                )
            ));
        } else {
            $threads[$i]->setResponses($responseDao->getResponseListByThreadUid(
                $threads[$i]->getThreadUid(),
                $responseStart,
                $responseLimit
            ));
        }
    }
} catch (PDOException $e) {
    $logger->error('index.php: Database exception: {msg}', ['msg' => $e->getMessage()]);
    $exceptionHandler->handle('/database', $e);
} catch (DataAccessException $e) {
    $logger->error('index.php: Data access exception: {msg}', ['msg' => $e->getMessage()]);
    $exceptionHandler->handle('/data-access', $e);
}
?>

<html>
<head>
    <meta charset="UTF-8"/>
    <title>인덱스 :: <?= $board['name'] ?></title>
    <link rel="stylesheet" type="text/css" href="<?= $config['site']['baseUrl'] ?>/asset/<?= $board['style'] ?>"/>
    <script type="text/javascript" src="<?= $config['site']['baseUrl'] ?>/asset/main.js"></script>
</head>
<body>
<?php require(__DIR__ . '/template/menu.php'); ?>
<div id="top"></div>
<?php
if (sizeof($threads) > 0) {
    for ($i = 0; $i < min($board['maxThreadView'], sizeof($threads)); $i++) {
        if (sizeof($threads[$i]->getResponses()) < 1) {
            continue;
        }
        $thread = $threads[$i];
        require(__DIR__ . '/template/thread.php');
    }
}
?>
<?php
require(__DIR__ . '/template/create_thread.php');
?>
<?php
require(__DIR__ . '/template/version.php');
?>
<div id="bottom"></div>
</body>
</html>
