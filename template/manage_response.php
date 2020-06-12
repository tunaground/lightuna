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
    $maskButtonHtml = <<<HTML
<button class="response_mask" onclick="maskResponse('$baseUrl', $threadUid, $responseUid)">
Mask
</button>
HTML;
} else {
    $maskButtonHtml = '';
}
?>
<div class="response"
     id="response_<?= $response->getThreadUid() ?>_<?= $response->getSequence() ?>"
     data-board-uid="<?= $board['uid'] ?>"
     data-thread-uid="<?= $response->getThreadUid() ?>"
     data-response-sequence="<?= $response->getSequence() ?>">
    <p class="response_info">
        <input type="checkbox"
               id="check_response_<?= $response->getResponseUid() ?>"
               class="check_response"
               name="responses"
               value="<?= $response->getResponseUid() ?>"/>
        <label for="check_response_<?= $response->getResponseUid() ?>">
            :
            <span class="response_sequence"><?= $response->getSequence() ?></span>
            <span class="response_owner"><?= $response->getUserName() ?></span>
        </label>
        <span class="response_owner_id">
            <a href="#"
               onclick="banUserId('<?= $baseUrl ?>', <?= $response->getThreadUid() ?>, <?= $response->getResponseUid() ?>)">
                (<?= $response->getUserId() ?>)
            </a>
        </span>
        <span class="response_mask_button">
        </span>
        <?php if ($response->getMask()) { ?>
            <span class="response_mask_mark">Masked</span>
        <?php } else { ?>
            <?= $maskButtonHtml ?>
        <?php } ?>
    </p>
    <p class="response_create_date"><?= $createDate ?></p>
    <?= $image ?>
    <div class="content"><?= $response->getContent() ?></div>
</div>
