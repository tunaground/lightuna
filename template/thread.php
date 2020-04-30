<div class="thread">
    <?php
    require(__DIR__ . '/thread_header.php');
    ?>
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
