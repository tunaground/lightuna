function hideResponse(root, threadUid, responseUid) {
    const threadPassword = prompt('Thread password?');
    const xhr = new XMLHttpRequest();
    xhr.open('POST', root + '/console.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json"');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                console.log(xhr.responseText);
                const response = JSON.parse(xhr.responseText);
                if (response.result === true) {
                    alert('Response has been deleted.');
                } else {
                    alert('Failed: ' + response.message);
                }
            } else {
                alert('Failed...');
            }
        }
    };
    xhr.send(JSON.stringify({
        'action': 'hideResponse',
        'payload': {
            'threadUid': threadUid,
            'responseUid': responseUid,
            'threadPassword': threadPassword
        }
    }));
}

document.addEventListener('DOMContentLoaded', function () {
    const serverInfo = document.getElementById('server_info');
    const baseUrl = serverInfo.dataset.baseUrl;

    const contentForms = document.getElementsByClassName('post_form_content');
    Array.prototype.forEach.call(contentForms, function (el) {
        const defaultHeight = el.offsetHeight;
        el.addEventListener('input', function () {
            el.style.height = (el.scrollHeight < defaultHeight) ? defaultHeight + 'px' : el.scrollHeight + 'px';
        })
    });

    const nameForms = document.getElementsByClassName('post_form_name');
    Array.prototype.forEach.call(nameForms, function (el) {
        const threadUid = el.dataset.threadUid;
        el.value = sessionStorage.getItem(threadUid + '-name');
        el.addEventListener('input', function () {
            sessionStorage.setItem(threadUid + '-name', this.value);
        });
    });

    const consoleForm = document.getElementsByClassName('post_form_console');
    Array.prototype.forEach.call(consoleForm, function (el) {
        const threadUid = el.dataset.threadUid;
        el.value = sessionStorage.getItem(threadUid + '-console');
        el.addEventListener('input', function () {
            sessionStorage.setItem(threadUid + '-console', this.value);
        });

        if (el.value.includes('aa')) {
            el.nextElementSibling.classList.add('mona')
        }

        el.addEventListener('input', function () {
            if (this.value.includes('aa')) {
                this.nextElementSibling.classList.add('mona')
            } else {
                this.nextElementSibling.classList.remove('mona')
            }
        })
    });


    const content = document.getElementsByClassName('content');
    Array.prototype.forEach.call(content, function (el) {
        const parentElement = el.parentElement;
        el.innerHTML = el.innerHTML.replace(
            /([a-z]*)&gt;([0-9]*)&gt;([0-9]*)-?([0-9]*)/,
            function (match, boardUid, threadUid, responseStart, responseEnd) {
                boardUid = (boardUid === '') ? parentElement.dataset.boardUid : boardUid;
                threadUid = (threadUid === '') ? parentElement.dataset.threadUid : threadUid;
                const inPageAnchor = 'response_' + threadUid + '_' + responseStart;
                if (responseEnd === ''
                    && document.getElementById(inPageAnchor)) {
                    return '<a href="#' + inPageAnchor + '">' + match + '</a>';
                } else {
                    return '<a href="'
                        + baseUrl
                        + '/trace.php/'
                        + boardUid
                        + '/'
                        + threadUid
                        + '/'
                        + responseStart
                        + '/'
                        + responseEnd
                        + '">'
                        + match
                        + '</a>';
                }
            }
        )
    })
});