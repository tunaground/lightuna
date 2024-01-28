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

function ncrToChar(text) {
    return text.replace(/&#([^;]+);/gm, function (_, match) {
        return String.fromCharCode(match);
    });
}

$(document).ready(function () {
    $(".response_content").each(function (i) {
        pBoardId = $(this).parents('.thread').data('board-id')
        pThreadId = $(this).parents('.thread').data('thread-id')
        pResponseSequence = $(this).parents('.response').data('response-sequence')

        $(this).html(setLink($(this)))
        $(this).html(setAnchor($(this)))
    })

    $('.post_form_content').on('input', function () {
        $(this).css('height',
            ($(this).prop('scrollHeight') < $(this).prop('offsetHeight'))
                ? $(this).prop('offsetHeight')
                : $(this).prop('scrollHeight') + 'px')
    })

    $('body').on('click', 'button.delete', function (e) {
        responseId = $(this).data('response-id')

        $.ajax({
            type: 'post',
            url: '/api/v1/delete/response',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({
                id: responseId,
                password: prompt('password')
            }),
            success: function (result) {
                alert(result.message)
            },
            error: function (request, status, error) {
                alert(error)
            }
        })
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
                    clone.find('.response_sequence').html(
                        `<a href="/trace/${boardId}/${threadId}/${response.sequence}">${response.sequence}</a>`
                    )
                    clone.find('.response_user_name').html(response.username)
                    clone.find('.response_user_id').html(`(${response.userId})`)
                    clone.find('.response_date').html((response.deletedAt == null)
                        ? `${response.createdAt}`
                        : `${response.createdAt} (${response.deletedAt})`)
                    clone.find('.response_content').html(response.content)
                    clone.html(setLink(clone))
                    clone.html(setAnchor(clone))
                    clone.addClass('anchor_response')
                    console.log(response.attachment)
                    if (response.attachment != '') {
                        attachment = `/userdata/${boardId}/${threadId}/images/${response.attachment}`
                        thumbnail_file = response.attachment.replace(/\.[^\.]+$/, '.jpg')
                        thumbnail = `/userdata/${boardId}/${threadId}/thumbnails/${thumbnail_file}`
                        $(`<a href="${attachment}"><img src="${thumbnail}"></a>`)
                            .insertBefore(clone.find('.response_content'))
                    }
                    curResponse.prepend(clone)
                })
            },
            error: function (request, status, error) {
                alert(error)
            }
        })
    })
})