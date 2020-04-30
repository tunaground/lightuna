<?php
/**
 * @var \Lightuna\Object\Response $response
 */
if (!isset($response)) {
    return;
}

$customWeek = array('내일 월요일', '모두 수고..', 'FIRE!', '水', '거의 끝나감', '불탄다..!', '파란날');
$date = $response->getCreateDate()->format('Y-m-d');
$week = $customWeek[$response->getCreateDate()->format('w')];
$time = $response->getCreateDate()->format('H:i:s');
$createDate = "{$date} ({$week}) $time";

if ($response->getAttachment() !== '') {
    $imagePath = $config['site']['baseUrl']
        . $config['site']['imageUploadPrefix']
        . '/image/'
        . $response->getAttachment();
    $thumbPath = $config['site']['baseUrl']
        . $config['site']['imageUploadPrefix']
        . '/thumb/'
        . $response->getAttachment();
    $image = <<<HTML
<a href="$imagePath">
    <img src="$thumbPath"/>
</a>
HTML;
} else {
    $image = '';
}

if ($response->getSequence() > 0) {
    $baseUrl = $config['site']['baseUrl'];
    $threadUid = $response->getThreadUid();
    $responseUid = $response->getResponseUid();
    $hideButtonHtml = <<<HTML
<button class="response_hide" onclick="hideResponse('$baseUrl', $threadUid, $responseUid)">
Hide
</button>
HTML;
} else {
    $hideButtonHtml = '';
}
?>
<div class="response">
    <p class="response_info">
        :
        <span class="response_sequence"><?= $response->getSequence() ?></span>
        <span class="response_owner"><?= $response->getUserName() ?></span>
        <span class="response_owner_id">(<?= $response->getUserId() ?>)</span>
        <span class="response_hide_button">
        <?= $hideButtonHtml ?>
        </span>
    </p>
    <p class="response_create_date"><?= $createDate ?></p>
    <?= $image ?>
    <div class="content"><?= $response->getContent() ?></div>
</div>
