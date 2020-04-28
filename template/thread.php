<div class="thread">
    <a name="<?= $thread->getSequence() ?>"></a>
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
    <div class="thread_body">
        <?php
        foreach ($thread->getResponses() as $response) {
            require(__DIR__ . '/response.php');
        }
        ?>
    </div>
    <fieldset class="response">
        <form action="<?= $config['site']['baseUrl'] ?>/post.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="type" value="response">
            <input type="hidden" name="board_uid" value="<?= $board['uid'] ?>">
            <input type="hidden" name="thread_uid" value="<?= $thread->getThreadUid() ?>">
            <input type="hidden" name="return_url" value="<?= $_SERVER['REQUEST_URI'] ?>">
            <input type="text" name="name" placeholder="나메(60자까지)" value="">
            <input type="text" name="console" placeholder="콘솔" value="">
            <textarea name="content" placeholder="본문(20000자까지)" spellcheck="false" required=""></textarea>
            <input type="file" name="attachment">
            <input type="submit" value="마솝">
        </form>
    </fieldset>
</div>
