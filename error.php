<?php
define('FRONT_PAGE', true);

require('./require.php');
?>

<html>
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>오류 :: <?= $board['name'] ?></title>
</head>
<body>
오류: <?=$_SERVER['PATH_INFO']?>
<?php
require(__DIR__ . '/template/version.php');
?>
</body>
</html>
