<?php
$baseUrl = $config['site']['baseUrl'];
$threadUid = $thread->getThreadUid();
$manageThreadButton = <<<HTML
<button class="button_default" onclick="manageThread('$baseUrl', '$boardUid', $threadUid)">Manage</button>
HTML;
?>
<div class="thread_head" id="thread_<?= $thread->getSequence() ?>">
    <p class="thread_title">
        <span>
        &gt;<?= $thread->getThreadUid() ?>&gt;
        </span>
        <a href="<?= $config['site']['baseUrl'] ?>/trace.php/<?= $board['uid'] ?>/<?= $thread->getThreadUid() ?>/recent">
            <?= $thread->getTitle() ?>
        </a>
        ::
        <a href="<?= $config['site']['baseUrl'] ?>/trace.php/<?= $board['uid'] ?>/<?= $thread->getThreadUid() ?>">
            <?= $thread->getSize() ?>
        </a>
    </p>
    <p class="thread_owner">
        <?= $thread->getUserName() ?>
        <?= $manageThreadButton ?>
    </p>
    <p class="thread_create_date"><?= $thread->getCreateDate()->format('Y-m-d H:i:s') ?> - <?= $thread->getUpdateDate()->format('Y-m-d H:i:s') ?></p>
</div>
