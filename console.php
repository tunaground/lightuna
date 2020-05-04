<?php

use Lightuna\Database\DataSource;
use Lightuna\Database\MariadbThreadDao;
use Lightuna\Database\MariadbResponseDao;
use Lightuna\Database\MariadbArcResponseDao;
use Lightuna\Database\MysqlThreadDao;
use Lightuna\Database\MysqlResponseDao;
use Lightuna\Database\MysqlArcResponseDao;
use Lightuna\Service\ResponseService;
use Lightuna\Service\PostService;
use Lightuna\Util\ContextParser;
use Lightuna\Log\Logger;
use Lightuna\Object\Board;

define('FRONT_PAGE', true);

require('./require.php');

ini_set('display_errors', false);

header('Content-Type: application/json');

$contextParser = new ContextParser();
$logger = new Logger($config['site']['logFilePath'], $contextParser);
$dataSource = new DataSource(
    $config['database']['host'],
    $config['database']['port'],
    $config['database']['user'],
    $config['database']['password'],
    $config['database']['schema'],
    $config['database']['options'],
    $logger
);
if ($config['database']['type'] === 'mysql') {
    $threadDao = new MysqlThreadDao($dataSource, $logger);
    $responseDao = new MysqlResponseDao($dataSource, $logger);
    $arcResponseDao = new MysqlArcResponseDao($dataSource, $logger);
} else {
    $threadDao = new MariadbThreadDao($dataSource, $logger);
    $responseDao = new MariadbResponseDao($dataSource, $logger);
    $arcResponseDao = new MariadbArcResponseDao($dataSource, $logger);
}
$responseService = new ResponseService($dataSource, $threadDao, $responseDao, $arcResponseDao);

$data = json_decode(file_get_contents('php://input'));

if ($data->action === 'hideResponse') {
    $result = [
        'result' => false,
        'message' => ''
    ];
    $threadUid = (int) $data->payload->threadUid;
    $responseUid = (int) $data->payload->responseUid;
    $threadPassword = $data->payload->threadPassword;
    try {
        $responseService->archiveResponse(
            $threadUid,
            $responseUid,
            $threadPassword
        );
        $result['result'] = true;
    } catch (Exception $e) {
        $result['result'] = false;
        $result['message'] = $e->getMessage();
    } finally {
        echo json_encode($result, JSON_FORCE_OBJECT);
    }
}

if ($data->action === 'testResponse') {
    $result = [
        'result' => false,
        'message' => '',
        'payload' => [],
    ];
    
    try {
        $board = new Board($config, $data->payload->boardUid);
        $postService = new PostService($dataSource, $threadDao, $responseDao, $board);
        $result['payload'] = $postService->testResponse(
            htmlspecialchars($data->payload->userName),
            explode('.', $data->payload->console),
            str_replace(array("\r\n", "\r", "\n"), '<br/>', htmlspecialchars($data->payload->content)),
            $_SERVER['REMOTE_ADDR'],
            new DateTime()
        );
        $result['result'] = true;
    } catch (Exception $e) {
        $result['result'] = false;
        $result['messsage'] = $e->getMessage();
    } finally {
        echo json_encode($result, JSON_FORCE_OBJECT);
    }
}
