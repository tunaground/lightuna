<div class="thread_head">
    <p class="thread_order"><?= $thread->getSequence() ?></p>
    <p class="thread_id"><?= $thread->getThreadUid() ?></p>
    <p class="thread_owner"><?= $thread->getUserName() ?></p>
    <p class="thread_title">
        <a href="<?= $config['site']['baseUrl'] ?>/trace.php/<?= $board['uid'] ?>/<?= $thread->getThreadUid() ?>/recent">
            <?= $thread->getTitle() ?>
        </a>
        ::
        <a href="<?= $config['site']['baseUrl'] ?>/trace.php/<?= $board['uid'] ?>/<?= $thread->getThreadUid() ?>">
            <?= $thread->getSize() ?>
        </a>
    </p>
    <p class="thread_careted_date"><?= $thread->getCreateDate()->format('Y-m-d H:i:s') ?></p>
    <p class="thread_refresh_date"><?= $thread->getUpdateDate()->format('Y-m-d H:i:s') ?></p>
</div>
