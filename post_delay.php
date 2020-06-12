<?php
use Lightuna\Util\Redirection;

const FRONT_PAGE = true;

require('./require.php');
?>
<html>
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>작성완료</title>
</head>
<body>
바아아아아아아아아아아아아.<br/>
작성 완료. 잠깐만 기다려.
</body>
</html>
<?php
Redirection::temporaryDelay($_GET['return_url'], 2);
?>
