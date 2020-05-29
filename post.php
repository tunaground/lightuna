<?php

use Lightuna\Database\DataSource;
use Lightuna\Database\MariadbThreadDao;
use Lightuna\Database\MariadbResponseDao;
use Lightuna\Database\MariadbBanDao;
use Lightuna\Database\MysqlThreadDao;
use Lightuna\Database\MysqlResponseDao;
use Lightuna\Database\MysqlBanDao;
use Lightuna\Object\Board;
use Lightuna\Service\AttachmentService;
use Lightuna\Service\PostService;
use Lightuna\Util\NetworkUtil;
use Lightuna\Util\ThumbUtil;
use Lightuna\Util\Redirection;
use Lightuna\Util\ContextParser;
use Lightuna\Log\Logger;
use Lightuna\Exception\DataAccessException;
use Lightuna\Exception\InvalidUserInputException;
use Lightuna\Util\ExceptionHandler;

const FRONT_PAGE = true;

require('./require.php');

$contextParser = new ContextParser();
$logger = new Logger($config['site']['logFilePath'], $contextParser);
$exceptionHandler = new ExceptionHandler($config, $logger);
$boardUid = $_POST['board_uid'];
$board = new Board($config, $boardUid);
$dataSource = new DataSource(
    $config['database']['host'],
    $config['database']['port'],
    $config['database']['user'],
    $config['database']['password'],
    $config['database']['schema'],
    $config['database']['options'],
    $logger
);
$netUtil = new NetworkUtil();

if ($config['database']['type'] === 'mysql') {
    $threadDao = new MysqlThreadDao($dataSource, $logger);
    $responseDao = new MysqlResponseDao($dataSource, $logger);
    $banDao = new MysqlBanDao($dataSource, $logger);
} else {
    $threadDao = new MariadbThreadDao($dataSource, $logger);
    $responseDao = new MariadbResponseDao($dataSource, $logger);
    $banDao = new MariadbBanDao($dataSource, $logger);
}
$postService = new PostService($dataSource, $threadDao, $responseDao, $banDao, $board);
$attachmentService = new AttachmentService($config, $board, new ThumbUtil());

$type = $_POST['type'];
$userName = htmlspecialchars($_POST['name']);
if ($userName === '') {
    $userName = $board['userName'];
}
$console = explode('.', $_POST['console']);
$content = str_replace(array("\r\n", "\r", "\n"), '<br/>', htmlspecialchars($_POST['content']));
$returnUrl = $_POST['return_url'];
$ip = $netUtil->getIP();
$currentDateTime = new DateTime();

try {
    if ($_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
        $attachment = $attachmentService->upload($_FILES['attachment']);
    } else {
        $attachment = '';
    }

    if ($type === 'thread') {
        $title = $_POST['title'];
        $password = $_POST['password'];
        $postService->postThread(
            $userName,
            $console,
            $content,
            $attachment,
            $title,
            $password,
            $ip,
            $currentDateTime
        );
    } else {
        $threadUid = $_POST['thread_uid'];
        $postService->postResponse(
            $threadUid,
            $userName,
            $console,
            $content,
            $attachment,
            $ip,
            $currentDateTime
        );
    }
    if (in_array('relay', $console, true)) {
        $returnUrl .= '#relay';
    }
    Redirection::temporary($returnUrl);
} catch (PDOException $e) {
    $logger->error('post.php: Database exception: {msg}', ['msg' => $e->getMessage()]);
    $exceptionHandler->handle('/database', $e);
} catch (DataAccessException $e) {
    $logger->error('post.php: Data access exception: {msg}', ['msg' => $e->getMessage()]);
    $exceptionHandler->handle('/data-access', $e);
} catch (InvalidUserInputException $e) {
    $logger->notice('post.php: Invalid user input exception: {msg}', ['msg' => $e->getMessage()]);
    echo $e->getMessage();
    $thread = $threadDao->getThreadbyThreadUid($threadUid);
    $content = $_POST['content'];
}
?>
<html>
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
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
<?php require(__DIR__ . '/template/create_response.php'); ?>
</body>
</html>
