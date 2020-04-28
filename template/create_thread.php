<div class="create_thread">
    <fieldset class="create_thread">
        <form action="<?= $config['site']['baseUrl'] ?>/post.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="type" value="thread">
            <input type="hidden" name="board_uid" value="<?= $board['uid'] ?>">
            <input type="hidden" name="return_url" value="<?= $_SERVER['REQUEST_URI'] ?>">
            <input type="text" name="title" placeholder="타이틀" value="">
            <input type="password" name="password" placeholder="암호" value="">
            <input type="text" name="name" placeholder="나메(60자까지)" value="">
            <input type="text" name="console" placeholder="콘솔" value="">
            <textarea name="content" placeholder="본문(20000자까지)" spellcheck="false" required=""></textarea>
            <input type="file" name="attachment">
            <input type="submit" value="마솝">
        </form>
    </fieldset>
</div>
