<?php

use Lightuna\Object\Board;
use Lightuna\Database\DataSource;
use Lightuna\Database\MariadbThreadDao;
use Lightuna\Database\MariadbResponseDao;
use Lightuna\Database\MysqlThreadDao;
use Lightuna\Database\MysqlResponseDao;
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

$uriParser = new UriParser($config, $_SERVER['REQUEST_URI']);
$board = new Board($config, $uriParser->getBoardUid());
if ($config['database']['type'] === 'mysql') {
    $threadDao = new MysqlThreadDao($dataSource, $logger);
    $responseDao = new MysqlResponseDao($dataSource, $logger);
} else {
    $threadDao = new MariadbThreadDao($dataSource, $logger);
    $responseDao = new MariadbResponseDao($dataSource, $logger);
}

$threadUid = $uriParser->getThreadUid();
try {
    $thread = $threadDao->getThreadByThreadUid($threadUid);
} catch (PDOException $e) {
    $logger->error('index.php: Database exception: {msg}', ['msg' => $e->getMessage()]);
    $exceptionHandler->handle('/database', $e);
} catch (DataAccessException $e) {
    $logger->error('trace.php: Data access exception: {msg}', ['msg' => $e->getMessage()]);
    $exceptionHandler->handle('/data-access', $e);
} catch (InvalidUserInputException $e) {
    $logger->debug('trace.php: Invalid Thread UID: {msg}', ['msg' => $e->getMessage()]);
    $exceptionHandler->handle('/invalid-thread-uid', $e);
}

try {
    $thread->setSize($threadDao->getThreadSize($thread->getThreadUid()) - 1);
    $thread->setSequence(0);

    if ($uriParser->isTraceRecent()) {
        $responseStart = max(0, $thread->getSize() - $board['maxResponseView']);
        $responseLimit = $board['maxResponseView'];
    } else {
        try {
            $responseStart = $uriParser->getResponseStart();
            $responseLimit = $uriParser->getResponseEnd($responseStart) - $responseStart + 1;
        } catch (OutOfBoundsException $e) {
            $responseStart = 1;
            $responseLimit = $thread->getSize();
        }
    }
    if ($responseStart === 0) {
        $thread->setResponses(
            $responseDao->getResponseListByThreadUid(
                $thread->getThreadUid(),
                $responseStart,
                $responseLimit
            )
        );
    } else {
        $thread->setResponses(array_merge(
            $responseDao->getResponseListByThreadUid($thread->getThreadUid(), 0, 1),
            $responseDao->getResponseListByThreadUid($thread->getThreadUid(), $responseStart, $responseLimit)
        ));
    }
} catch (PDOException $e) {
    $logger->error('index.php: Database exception: {msg}', ['msg' => $e->getMessage()]);
    $exceptionHandler->handle('/database', $e);
} catch (DataAccessException $e) {
    $logger->error('trace.php: Data access exception: {msg}', ['msg' => $e->getMessage()]);
    $exceptionHandler->handle('/data-access', $e);
}
?>

<html>
<head>
    <meta charset="UTF-8"/>
    <title>추적중 : <?= $thread->getTitle() ?> : <?= $board['name'] ?></title>
    <link rel="stylesheet" type="text/css" href="<?= $config['site']['baseUrl'] ?>/asset/<?= $board['style'] ?>"/>
    <script type="text/javascript" src="<?= $config['site']['baseUrl'] ?>/asset/main.js"></script>
</head>
<body>
<?php require(__DIR__ . '/template/menu.php'); ?>
<div id="top"></div>
<?php require(__DIR__ . '/template/thread.php'); ?>
<?php require(__DIR__ . '/template/version.php'); ?>
<div id="bottom"></div>
</body>
</html>
