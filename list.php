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
use Lightuna\Exception\InvalidUserInputException;
use Lightuna\Util\ExceptionHandler;
use Lightuna\Service\SearchService;
use Lightuna\Common\SearchType;

define('FRONT_PAGE', true);

require('./require.php');

$returnUrl = $_SERVER['REQUEST_URI'];
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
    $listPage = $uriParser->getListPage();
    $previousPage = $listPage - 1;
    $nextPage = $listPage + 1;
} catch (UnexpectedValueException $e) {
    $logger->debug('list.php: Invalid Board UID: {msg}', ['msg' => $e->getMessage()]);
    $exceptionHandler->handle('/invalid-board-uid', $e);
} catch (InvalidUserInputException $e){
    $exceptionHandler->handle('/invalid-uri', $e);
}

if ($config['database']['type'] === 'mysql') {
    $threadDao = new MysqlThreadDao($dataSource, $logger);
    $responseDao = new MysqlResponseDao($dataSource, $logger);
} else {
    $threadDao = new MariadbThreadDao($dataSource, $logger);
    $responseDao = new MariadbResponseDao($dataSource, $logger);
}

$searchService = new SearchService($board, $threadDao, $responseDao);

if (isset($_GET['search_type']) && isset($_GET['keyword'])) {
    $searchType = $_GET['search_type'];
    $keyword = $_GET['keyword'];
} else {
    $searchType = SearchType::NONE;
    $keyword = '';
}
$start = ($listPage - 1) * $board['maxThreadListView'];
$limit = $board['maxThreadListView'];

try {
    if ($searchType === SearchType::THREAD_TITLE) {
        $threads = $searchService->findByThreadTitle($keyword, $start, $limit);
    } elseif ($searchType === SearchType::THREAD_OWNER) {
        $threads = $searchService->findByThreadOwner($keyword, $start, $limit);
    // } elseif ($searchType === SearchType::RESPONSE_USER_NAME) {
    //     $responses = $searchService->findByResponseUserName($keyword, $start, $limit);
    // } elseif ($searchType === SearchType::RESPONSE_USER_ID) {
    //     $responses = $searchService->findByResponseUserId($keyword, $start, $);
    } else {
        $threads = $threadDao->getThreadListByBoardUid($board['id'], $board['maxThreadListView'], $start);
        for ($i = 0; $i < sizeof($threads); $i++) {
            $threads[$i]->setSize($threadDao->getLastResponseSequence($threads[$i]->getThreadUid()));
            $threads[$i]->setSequence($i + 1 + $start);
        }
    }
} catch (PDOException $e) {
    $logger->error('index.php: Database exception: {msg}', ['msg' => $e->getMessage()]);
    $exceptionHandler->handle('/database', $e);
} catch (DataAccessException $e) {
    $logger->error('index.php: Data access exception: {msg}', ['msg' => $e->getMessage()]);
    $exceptionHandler->handle('/data-access', $e);
} catch (InvalidUserInputException $e) {
    $exceptionHandler->handle('/invalid-user-input', $e);
}

$baseUrl = $config['site']['baseUrl'];
$boardUid = $board['uid'];
$previousPageHtml = '';
$nextPageHtml = '';

if ($previousPage > 0) {
    $previousPageHtml = <<<HTML
<div class="thread_list_item center">
    <a href="$baseUrl/list.php/$boardUid/$previousPage?{$_SERVER['QUERY_STRING']}"><p>이전 페이지</p></a>
</div>
HTML;
}

if (sizeof($threads) === $board['maxThreadListView']) {
    $nextPageHtml = <<<HTML
<div class="thread_list_item center">
    <a href="$baseUrl/list.php/$boardUid/$nextPage?{$_SERVER['QUERY_STRING']}"><p>다음 페이지</p></a>
</div>
HTML;
}
?>

<html>
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="format-detection" content="telephone=no">
    <title>검색 :: <?= $board['name'] ?></title>
    <link rel="stylesheet" type="text/css" href="<?= $config['site']['baseUrl'] ?>/asset/<?= $board['style'] ?>"/>
</head>
<body>
<?php require(__DIR__ . '/template/menu.php'); ?>
<div id="top"></div>
<?php require(__DIR__ . '/template/search.php'); ?>
<div id="thread_list_container">
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
</div>
<?php
require(__DIR__ . '/template/version.php');
?>
<div id="bottom"></div>
</body>
</html>
