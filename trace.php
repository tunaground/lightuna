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
        $responseEnd = $board['maxResponseView'];
    } else {
        try {
            $responseStart = $uriParser->getResponseStart();
            $responseEnd = $uriParser->getResponseEnd($responseStart);
        } catch (OutOfBoundsException $e) {
            $responseStart = 1;
            $responseEnd = $thread->getSize() + 1;
        }
    }
    if ($responseStart === 0) {
        $thread->setResponses(
            $responseDao->getResponseListByThreadUid(
                $thread->getThreadUid(),
                $responseStart,
                $responseEnd
            )
        );
    } else {
        $thread->setResponses(array_merge(
            $responseDao->getResponseListBySequence($thread->getThreadUid(), 0, 0),
            $responseDao->getResponseListBySequence($thread->getThreadUid(), $responseStart, $responseEnd)
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
</head>
<body>
<?php require(__DIR__ . '/template/menu.php'); ?>
<?php require(__DIR__ . '/template/thread.php'); ?>
<?php require(__DIR__ . '/template/version.php'); ?>
</body>
</html>
