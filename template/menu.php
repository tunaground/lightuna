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
        <li><a href="#top">맨 위</a></li>
        <li><a href="#bottom">맨 아래</a></li>
        <?= $list ?>
    </ul>
</nav>
