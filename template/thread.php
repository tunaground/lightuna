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
    <fieldset class="post_form">
        <form action="<?= $config['site']['baseUrl'] ?>/post.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="type" value="response">
            <input type="hidden" name="board_uid" value="<?= $board['uid'] ?>">
            <input type="hidden" name="thread_uid" value="<?= $thread->getThreadUid() ?>">
            <input type="hidden" name="return_url" value="<?= $_SERVER['REQUEST_URI'] ?>">
            <input type="text" name="name" placeholder="나메(60자까지)" value="" class="post_form_default">
            <input type="text" name="console" placeholder="콘솔" value="" class="post_form_default">
            <textarea name="content"
                      placeholder="본문(<?= $board['maxContentLength'] ?>자까지)"
                      spellcheck="false"
                      required=""
                      class="post_form_content"></textarea>
            <input type="file" name="attachment" class="post_form_attachment">
            <input type="submit" value="작성" class="post_form_submit">
        </form>
    </fieldset>
</div>
