<div class="create_thread">
    <fieldset class="post_form post_form_thread">
        <form action="<?= $config['site']['baseUrl'] ?>/post.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="type" value="thread">
            <input type="hidden" name="board_uid" value="<?= $board['uid'] ?>">
            <input type="hidden" name="return_url" value="<?= $_SERVER['REQUEST_URI'] ?>">
            <input type="text" name="title" placeholder="타이틀" value="" class="post_form_default">
            <input type="password" name="password" placeholder="암호" value="" class="post_form_default">
            <input type="text" name="name" placeholder="나메(<?= $board['maxNameLength'] ?>자까지)" value="" class="post_form_default">
            <input type="text" name="console" placeholder="콘솔" value="" class="post_form_default">
            <textarea name="content"
                      placeholder="본문(<?= $board['maxContentLength'] ?>자까지)"
                      spellcheck="false"
                      required=""
                      class="post_form_content"></textarea>
            <input type="file" name="attachment" class="post_form_attachment">
            <button class="thread_form_submit">작성</button>
        </form>
    </fieldset>
</div>
