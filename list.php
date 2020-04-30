<?php

use Lightuna\Database\DataSource;
use Lightuna\Database\MariadbThreadDao;
use Lightuna\Database\MysqlThreadDao;
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
} else {
    $threadDao = new MariadbThreadDao($dataSource, $logger);
}

$listPage = $uriParser->getListPage();
$previousPage = $listPage - 1;
$nextPage = $listPage + 1;
try {
    $start = ($listPage - 1) * $board['maxThreadListView'];
    $threads = $threadDao->getThreadListByBoardUid($board['id'], $board['maxThreadListView'], $start);
    for ($i = 0; $i < sizeof($threads); $i++) {
        $threads[$i]->setSize($threadDao->getLastResponseSequence($threads[$i]->getThreadUid()));
        $threads[$i]->setSequence($i + 1 + $start);
    }
} catch (PDOException $e) {
    $logger->error('index.php: Database exception: {msg}', ['msg' => $e->getMessage()]);
    $exceptionHandler->handle('/database', $e);
} catch (DataAccessException $e) {
    $logger->error('index.php: Data access exception: {msg}', ['msg' => $e->getMessage()]);
    $exceptionHandler->handle('/data-access', $e);
}


$baseUrl = $config['site']['baseUrl'];
$boardUid = $board['uid'];
$previousPageHtml = '';
$nextPageHtml = '';

if ($previousPage > 0) {
    $previousPageHtml = <<<HTML
<div class="thread_list_item center">
    <a href="$baseUrl/list.php/$boardUid/$previousPage"><p>이전 페이지</p></a>
</div>
HTML;
}

if (sizeof($threads) === $board['maxThreadListView']) {
    $nextPageHtml = <<<HTML
<div class="thread_list_item center">
    <a href="$baseUrl/list.php/$boardUid/$nextPage"><p>다음 페이지</p></a>
</div>
HTML;
}

?>

<html>
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>인덱스 :: <?= $board['name'] ?></title>
    <link rel="stylesheet" type="text/css" href="<?= $config['site']['baseUrl'] ?>/asset/<?= $board['style'] ?>"/>
</head>
<body>
<?php require(__DIR__ . '/template/menu.php'); ?>
<div id="top"></div>
<div id="thread_list">
    <?php
    if (sizeof($threads) > 0) {
        for ($i = 0; $i < sizeof($threads); $i++) {
            $thread = $threads[$i];
            $titleLink = "{$config['site']['baseUrl']}/trace.php/{$board['uid']}/{$thread->getThreadUid()}/recent";
            $sizeLink = "{$config['site']['baseUrl']}/trace.php/{$board['uid']}/{$thread->getThreadUid()}";
            $sequenceLink = '#';
            require(__DIR__ . '/template/thread_list_item.php');
        }
    }
    ?>
    <?= $previousPageHtml ?>
    <?= $nextPageHtml ?>
</div>
<?php
require(__DIR__ . '/template/version.php');
?>
<div id="bottom"></div>
</body>
</html>
