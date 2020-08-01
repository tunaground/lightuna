<?php
$baseUrl = $config['site']['baseUrl'];
$boardUids = array_filter(array_keys($config['boards']), function ($boardUid) {
    return ($boardUid !== '__default__');
});
$list = '';
foreach ($boardUids as $boardUid) {
    $boardName = $config['boards'][$boardUid]['name'];
    $list .= <<<HTML
<li><a href="$baseUrl/index.php/$boardUid">$boardName</a></li>
HTML;
}

$traceList = '';
if ($_SERVER['SCRIPT_NAME'] === "{$baseUrl}/trace.php") {
    $maxResponseView = $board['maxResponseView'];
    $traceList = <<<HTML
<li><a href="$baseUrl/trace.php/{$board['uid']}/$threadUid/recent">최근 $maxResponseView 보기</a></li>
<li><a href="$baseUrl/trace.php/{$board['uid']}/$threadUid">전부 보기</a></li>
HTML;
}
?>
<nav>
    <ul>
        <li><a href="#top">맨 위</a></li>
        <li><a href="#bottom">맨 아래</a></li>
        <?= $list ?>
        <?= $traceList ?>
    </ul>
</nav>
