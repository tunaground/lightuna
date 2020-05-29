<?php
/**
 * @var \Lightuna\Object\Response $response
 */
if (!isset($response)) {
    return;
}

$date = $response->getCreateDate()->format('Y-m-d');
$week = $board['customWeek'][$response->getCreateDate()->format('w')];
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
<div class="response"
     id="response_<?= $response->getThreadUid() ?>_<?= $response->getSequence() ?>"
     data-board-uid="<?= $board['uid'] ?>"
     data-thread-uid="<?= $response->getThreadUid() ?>"
     data-response-sequence="<?= $response->getSequence() ?>">
    <p class="response_info">
        :
        <span class="response_sequence"><?= $response->getSequence() ?></span>
        <span class="response_owner"><?= $response->getUserName() ?></span>
        <span class="response_owner_id">
            <a href="#" onclick="banUserId('<?= $baseUrl ?>', <?= $response->getThreadUid() ?>, <?= $response->getResponseUid() ?>)">
                (<?= $response->getUserId() ?>)
            </a>
        </span>
        <span class="response_hide_button">
        <?= $hideButtonHtml ?>
        </span>
    </p>
    <p class="response_create_date"><?= $createDate ?></p>
    <?= $image ?>
    <div class="content"><?= $response->getContent() ?></div>
</div>
