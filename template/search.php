<div class="search_form_container">
    <fieldset class="search_form">
        <form action="<?= $config['site']['baseUrl'] ?>/list.php/<?= $board['uid'] ?>" method="get" enctype="multipart/form-data">
            <select class="search_form_input search_type_input" name="search_type">
                <option value="thread_title" <?php if ($searchType === 'thread_title') { echo 'selected'; } ?>>스레드 제목</option>
                <option value="thread_owner" <?php if ($searchType === 'thread_owner') { echo 'selected'; } ?>>스레드 작성자</option>
            </select>
            <input class="search_form_input search_keyword_input" type="text" name="keyword" value="<?= $keyword ?>" />
            <button class="button_default search_form_input search_form_submit">검색</button>
        </form>
    </fieldset>
</div>
