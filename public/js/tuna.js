$(document).ready(function () {
    $(".response_content").each(function (i) {
        $(this).html($(this).html().replace(
            /https?:\/\/((?!www\.youtube\.com\/embed\/)(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b[-a-zA-Z0-9가-힣()@:%_\+;.~#?&//=]*)/g,
            function (match) {
                return `<a href="${match}" target="_blank">${match}</a>`;
            }
        ))
        $(this).html($(this).html().replace(
            /([a-z]*)&gt;([0-9]*)&gt;([0-9]*)-?([0-9]*)/gm,
            function (match, boardId, threadId, responseStart, responseEnd) {
                return `<a href="/trace/${boardId}/${threadId}/${responseStart}/${responseEnd}">${match}</a>`;
            }
        ))
    })
})