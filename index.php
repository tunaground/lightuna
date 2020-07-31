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
} catch (UnexpectedValueException $e) {
    $logger->debug('index.php: Invalid Board UID: {msg}', ['msg' => $e->getMessage()]);
    $exceptionHandler->handle($e);
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
        $lastResponseSequence = $threadDao->getLastResponseSequence($threads[$i]->getThreadUid());
        $dead = ($lastResponseSequence >= $board['maxResponseSize']);
        $threads[$i]->setSize($lastResponseSequence);
        $threads[$i]->setSequence($i + 1);
    }

    $threadViewCount = (count($threads) < $board['maxThreadView']) ? count($threads) : $board['maxThreadView'];
    for ($i = 0; $i < $threadViewCount; $i++) {
        $responseStart = max(0, $threads[$i]->getSize() - $board['maxResponseView']);
        $responseEnd = $threads[$i]->getSize();
        if ($responseStart > 0) {
            $threads[$i]->setResponses(array_merge(
                $responseDao->getResponseListBySequence(
                    $threads[$i]->getThreadUid(),
                    0,
                    0
                ),
                $responseDao->getResponseListBySequence(
                    $threads[$i]->getThreadUid(),
                    $responseStart,
                    $responseEnd
                )
            ));
        } else {
            $threads[$i]->setResponses($responseDao->getResponseListBySequence(
                $threads[$i]->getThreadUid(),
                $responseStart,
                $responseEnd
            ));
        }
    }
} catch (PDOException $e) {
    $logger->error('index.php: Database exception: {msg}', ['msg' => $e->getMessage()]);
    $exceptionHandler->handle(new PDOException(MSG_DATABASE_FAILED));
} catch (DataAccessException $e) {
    $logger->error('index.php: Data access exception: {msg}', ['msg' => $e->getMessage()]);
    $exceptionHandler->handle($e);
}

$searchType = SearchType::NONE;
$keyword = '';
?>

<html>
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="format-detection" content="telephone=no">
    <title>인덱스 :: <?= $board['name'] ?></title>
    <link rel="stylesheet" type="text/css" href="<?= $config['site']['baseUrl'] ?>/asset/<?= $board['style'] ?>"/>
    <script type="text/javascript" src="<?= $config['site']['baseUrl'] ?>/asset/main.js"></script>
</head>
<body>
<?php require(__DIR__ . '/template/menu.php'); ?>
<div id="top"></div>
<div id="server_info"
     data-base-url="<?= $config['site']['baseUrl'] ?>">
</div>
<?php require(__DIR__ . '/template/search.php'); ?>
<div id="thread_list_container">
    <div id="thread_list">
        <?php
        if (sizeof($threads) > 0) {
            for ($i = 0; $i < sizeof($threads); $i++) {
                $thread = $threads[$i];
                if ($thread->getSequence() < $board['maxThreadView']) {
                    $titleLink = "#thread_{$thread->getSequence()}";
                    $sizeLink = "{$config['site']['baseUrl']}/trace.php/{$board['uid']}/{$thread->getThreadUid()}/recent";
                    $sequenceLink = "{$config['site']['baseUrl']}/trace.php/{$board['uid']}/{$thread->getThreadUid()}";
                } else {
                    $titleLink = "{$config['site']['baseUrl']}/trace.php/{$board['uid']}/{$thread->getThreadUid()}/recent";
                    $sizeLink = "{$config['site']['baseUrl']}/trace.php/{$board['uid']}/{$thread->getThreadUid()}";
                    $sequenceLink = '#';
                }
                require(__DIR__ . '/template/thread_list_item.php');
            }
        }
        ?>
        <div class="thread_list_item center">
            <a href="<?= $config['site']['baseUrl'] ?>/list.php/<?= $board['uid'] ?>">
                <p>더 보기
                <p>
            </a>
        </div>
    </div>
</div>
<div id="thread_section">
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
</div>
<div id="create_thread_container">
    <?php
    require(__DIR__ . '/template/create_thread.php');
    ?>
</div>
<?php
require(__DIR__ . '/template/version.php');
?>
<div id="bottom"></div>
</body>
</html>
