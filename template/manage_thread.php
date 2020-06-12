<?php
$baseUrl = $config['site']['baseUrl'];
$threadUid = $thread->getThreadUid();
$maskCheckedButtonHtml = <<<HTML
<button class="response_mask" onclick="maskResponses('$baseUrl', $threadUid)">
Mask
</button>
HTML;
$unmaskCheckedButtonHtml = <<<HTML
<button class="response_unmask" onclick="unmaskResponses('$baseUrl', $threadUid)">
Unmask
</button>
HTML;
?>

<div class="thread"
     id="thread_<?= $thread->getThreadUid() ?>">
    <?php
    require(__DIR__ . '/thread_header.php');
    ?>
    <div class="thread_body">
        <?php
        foreach ($thread->getResponses() as $response) {
            require(__DIR__ . '/manage_response.php');
        }
        ?>
    </div>
    <div class="manage_button_set">
        <?= $maskCheckedButtonHtml ?>
        <?= $unmaskCheckedButtonHtml ?>
    </div>
    <?php if (!$thread->getDead()) { ?>
        <?php require(__DIR__ . '/create_response.php'); ?>
    <?php } else { ?>
        <div class="dead_sign">끝.</div>
    <?php } ?>
</div>
