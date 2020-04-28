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

$uriParser = new UriParser($config, $_SERVER['REQUEST_URI']);
$board = new Board($config, $uriParser->getBoardUid());
$threadDao = new ThreadDao($dataSource, $logger);
$responseService = new ResponseDao($dataSource, $logger);

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
            $responseService->getResponseListByThreadUid(
                $thread->getThreadUid(),
                $responseStart,
                $responseLimit
            )
        );
    } else {
        $thread->setResponses(array_merge(
            $responseService->getResponseListByThreadUid($thread->getThreadUid(), 0, 1),
            $responseService->getResponseListByThreadUid($thread->getThreadUid(), $responseStart, $responseLimit)
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
