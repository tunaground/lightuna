<?php
define('FRONT_PAGE', true);

$config = require('./require.php');
?>

<html>
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="format-detection" content="telephone=no">
    <title>오류 :: <?= $board['name'] ?></title>
</head>
<body>
<p>
    오류: <?= $_GET['msg'] ?>
</p>
<p>
    문의: <?= $config['site']['managerEmail'] ?>
</p>
<?php
require(__DIR__ . '/template/version.php');
?>
</body>
</html>
