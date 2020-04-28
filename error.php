<?php
define('FRONT_PAGE', true);

require('./require.php');
?>

<html>
<head>
    <meta charset="UTF-8"/>
    <title>오류 :: <?= $board['name'] ?></title>
</head>
<body>
오류: <?=$_SERVER['PATH_INFO']?>
<?php
require(__DIR__ . '/template/version.php');
?>
</body>
</html>
