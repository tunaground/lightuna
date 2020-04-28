<?php

use Lightuna\Database\DataSource;
use Lightuna\Database\MariadbThreadDao;
use Lightuna\Database\MariadbResponseDao;
use Lightuna\Database\MariadbArcResponseDao;
use Lightuna\Database\MysqlThreadDao;
use Lightuna\Database\MysqlResponseDao;
use Lightuna\Database\MysqlArcResponseDao;
use Lightuna\Service\ResponseService;
use Lightuna\Util\ContextParser;
use Lightuna\Log\Logger;

define('FRONT_PAGE', true);

require('./require.php');

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
    }
    echo json_encode($result, JSON_FORCE_OBJECT);
}
