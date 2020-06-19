<div class="thread_list_item">
    <p>
        <span class="thread_list_item_title">
        <a href="<?= $titleLink ?>">
            <?= $thread->getTitle() ?>
        </a>
        ::
        <a href="<?= $sizeLink ?>">
            <?= $thread->getSize() ?>
        </a>
    </span>
    </p>
    <p>
        <span class="thread_list_item_owner"><?= $thread->getUserName() ?></span>
        <span class="thread_list_item_update_date"><?= $thread->getUpdateDate()->format('Y-m-d H:i:s') ?></span>
    </p>
</div>
