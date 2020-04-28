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
?>
<nav>
    <ul>
        <li><a href="#top">상승기류</a></li>
        <li><a href="#bottom">어비스</a></li>
        <?= $list ?>
    </ul>
</nav>