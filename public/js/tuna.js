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
            return `<button class="anchor button-8"
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

function checkTextareaMona(el) {
    if (el.is(':checked')) {
        el.parents('form').find('textarea').addClass('mona')
    } else {
        el.parents('form').find('textarea').removeClass('mona')
    }
}

$(document).ready(function () {
    checkTextareaMona($('input[type=checkbox][id^="aa_"]'))

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

    $('body').on('click', 'input[type=checkbox][id^="aa_"]', function () {
        checkTextareaMona($(this))
    })

    $('body').on('click', 'button.delete', function (e) {
        curResponse = $(this).closest('.response')

        responseId = $(this).data('response-id')

        modal_container = $('.modal_container')
        modal = $('.modal')
        password_text = $('<p>Password</p>')
        password_input = $('<input type="text"/>')
        delete_button = $('<button>delete</button>')

        delete_button.on('click', function () {
            $.ajax({
                type: 'post',
                url: '/api/v1/delete/response',
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify({
                    id: responseId,
                    password: password_input.val()
                }),
                success: function (result) {
                    response = result.data.response

                    modal.html('')
                    result_text = $(`<p>${result.message}</p>`)
                    result_button = $('<button>close</button>')
                    result_button.on('click', function () {
                        modal_container.addClass('hide')
                    })
                    curResponse.find('.response_user_name').html('')
                    curResponse.find('.response_user_id').html('(deleted)')
                    curResponse.find('.response_user_id').addClass('response_deleted')
                    curResponse.find('.response_date').html((response.deletedAt == null)
                        ? `${response.createdAt}`
                        : `${response.createdAt} (${response.deletedAt})`)
                    curResponse.find('.response_content').html('')
                    curResponse.find('.response_youtube').remove()
                    curResponse.find('.response_attachment').remove()
                    curResponse.find('button.delete').addClass('restore')
                    curResponse.find('button.delete i').attr('class', 'iconoir-redo')
                    curResponse.find('button.delete').removeClass('delete')
                    modal.append(result_text)
                    modal.append(result_button)
                },
                error: function (request, status, error) {
                    alert(error)
                }
            })
        })

        modal.html('')
        modal.append(password_text)
        modal.append(password_input)
        modal.append(delete_button)
        modal_container.removeClass('hide')
    })

    $('body').on('click', 'button.restore', function (e) {
        curResponse = $(this).closest('.response')

        boardId = curResponse.parents('.response').data('board-id')
        threadId = curResponse.parents('.response').data('thread-id')
        responseId = $(this).data('response-id')

        modal_container = $('.modal_container')
        modal = $('.modal')
        password_text = $('<p>Password</p>')
        password_input = $('<input type="text"/>')
        restore_button = $('<button>delete</button>')

        restore_button.on('click', function () {
            $.ajax({
                type: 'post',
                url: '/api/v1/restore/response',
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify({
                    id: responseId,
                    password: password_input.val()
                }),
                success: function (result) {
                    response = result.data.response

                    modal.html('')
                    result_text = $(`<p>${result.message}</p>`)
                    result_button = $('<button>close</button>')
                    result_button.on('click', function () {
                        modal_container.addClass('hide')
                    })

                    curResponse.find('.response_content').remove()
                    curResponse.find('.response_user_name').html(response.username)
                    curResponse.find('.response_user_id').html(`(${response.userId})`)
                    curResponse.find('.response_user_id').removeClass('response_deleted')
                    curResponse.find('.response_date').html(response.createdAt)
                    response_content = $(`<article class="response_content"></article>`)
                    response_content.html(response.content)
                    if (response.youtube != '') {
                        youtubeId = response.youtube.replace(/https:\/\/www\.youtube.com\/watch\?v=([^&]+).*/, "$1");
                        response_youtube = $(`<p class="response_youtube">
                        <iframe class="youtube" src="https://www.youtube.com/embed/${youtubeId}" allowFullScreen></iframe>
                        </p>`)
                        curResponse.append(response_youtube)
                    }
                    if (response.attachment != '') {
                        attachment = `/userdata/${boardId}/${threadId}/images/${response.attachment}`
                        thumbnail_file = response.attachment.replace(/\.[^\.]+$/, '.jpg')
                        thumbnail = `/userdata/${boardId}/${threadId}/thumbnails/${thumbnail_file}`
                        response_attachment = $(`<p class="response_attachment"><a href="${attachment}"><img src="${thumbnail}"></a></p>`)
                        curResponse.append(response_attachment)
                    }

                    response_content.html(setLink(response_content))
                    response_content.html(setAnchor(response_content))
                    curResponse.append(response_content)
                    curResponse.find('button.restore').addClass('delete')
                    curResponse.find('button.restore i').attr('class', 'iconoir-trash')
                    curResponse.find('button.restore').removeClass('restore')
                    modal.append(result_text)
                    modal.append(result_button)
                },
                error: function (request, status, error) {
                    alert(error)
                }
            })
        })

        modal.html('')
        modal.append(password_text)
        modal.append(password_input)
        modal.append(restore_button)
        modal_container.removeClass('hide')
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
                    if (response.deletedAt == null) {
                        clone.find('.response_date').html(`${response.createdAt}`)
                    } else {
                        clone.find('.response_date').html(`${response.createdAt} (${response.deletedAt})`)
                        clone.find('.response_user_id').addClass('response_deleted')
                        clone.find('button.delete').addClass('restore')
                        clone.find('button.delete i').attr('class', 'iconoir-redo')
                        clone.find('button.delete').removeClass('delete')
                    }
                    clone.find('.response_content').html(response.content)
                    clone.html(setLink(clone))
                    clone.html(setAnchor(clone))
                    clone.addClass('anchor_response')
                    if (response.youtube != '') {
                        youtubeId = response.youtube.replace(/https:\/\/www\.youtube.com\/watch\?v=([^&]+).*/, "$1");
                        $(`<p class="response_youtube">
                        <iframe class="youtube" src="https://www.youtube.com/embed/${youtubeId}" allowFullScreen></iframe>
                        </p>`).insertBefore(clone.find('.response_content'))
                    }
                    if (response.attachment != '') {
                        attachment = `/userdata/${boardId}/${threadId}/images/${response.attachment}`
                        thumbnail_file = response.attachment.replace(/\.[^\.]+$/, '.jpg')
                        thumbnail = `/userdata/${boardId}/${threadId}/thumbnails/${thumbnail_file}`
                        $(`<p class="response_attachment"><a href="${attachment}"><img src="${thumbnail}"></a></p>`)
                            .insertBefore(clone.find('.response_content'))
                    }
                    clone.data('board-id', boardId)
                    clone.data('thread-id', threadId)
                    clone.find('button').attr('data-response-id', response.id)
                    console.log(clone.find('button'))
                    curResponse.prepend(clone)
                })
            },
            error: function (request, status, error) {
                alert(error)
            }
        })
    })
})