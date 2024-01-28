function setLink(el) {
    return el.html().replace(
        /https?:\/\/((?!www\.youtube\.com\/embed\/)(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b[-a-zA-Z0-9가-힣()@:%_\+;.~#?&//=]*)/g,
        function (match) {
            return `<a href="${match}" target="_blank">${match}</a>`;
        }
    )
}

function setAnchor(el) {
    return el.html().replace(
        /([a-z]*)&gt;([0-9]*)&gt;([0-9]*)-?([0-9]*)/gm,
        function (match, boardId, threadId, responseStart, responseEnd) {
            boardId = (boardId) ? boardId : pBoardId
            threadId = (threadId) ? threadId : pThreadId
            responseEnd = (responseEnd) ? responseEnd : responseStart
            return `<button class="anchor"
                data-board-id="${boardId}"
                data-thread-id="${threadId}"
                data-response-start="${responseStart}"
                data-response-end="${responseEnd}"
                >${match}</button>`;
        }
    )
}

$(document).ready(function () {
    $(".response_content").each(function (i) {
        pBoardId = $(this).parents('.thread').data('board-id')
        pThreadId = $(this).parents('.thread').data('thread-id')
        pResponseSequence = $(this).parents('.response').data('response-sequence')

        $(this).html(setLink($(this)))
        $(this).html(setAnchor($(this)))
    })

    $('body').on('click', 'button.anchor', function (e) {
        curResponse = $(this).closest('.response')
        if (curResponse.find('.anchor_response').length) {
            curResponse.find('.anchor_response').remove()
            return
        }

        boardId = $(this).data('board-id')
        threadId = $(this).data('thread-id')
        start = $(this).data('response-start')
        end = $(this).data('response-end')

        $.ajax({
            type: 'post',
            url: '/api/v1/get/response',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({boardId, threadId, start, end}),
            success: function (result) {
                result.responses.reverse().forEach(function (response) {
                    clone = curResponse.clone()
                    clone.find('.anchor_response').remove()
                    clone.find('.response_sequence').html(response.sequence)
                    clone.find('.response_user_name').html(response.username)
                    clone.find('.response_user_id').html(`(${response.userId})`)
                    clone.find('.response_date').html(response.createdAt.date)
                    clone.find('.response_content').html(response.content)
                    clone.html(setLink(clone))
                    clone.html(setAnchor(clone))
                    clone.addClass('anchor_response')
                    curResponse.prepend(clone)
                })
            },
            error: function (request, status, error) {
                alert(error)
            }
        })
    })
})