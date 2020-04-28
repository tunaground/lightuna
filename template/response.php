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
?>
<div class="response">
    <p class="response_info">
        :
        <span class="response_sequence"><?= $response->getSequence() ?></span>
        <span class="response_owner"><?= $response->getUserName() ?></span>
        <span class="response_owner_id"><?= $response->getUserId() ?></span>
        <button class="response_hide" onclick="hideResponse('<?= $config['site']['baseUrl'] ?>', <?= $response->getThreadUid() ?>, <?= $response->getResponseUid() ?>)">
            Hide
        </button>
    </p>
    <p class="response_create_date"><?= $createDate ?></p>
    <?= $image ?>
    <p class="content"><?= $response->getContent() ?></p>
</div>
