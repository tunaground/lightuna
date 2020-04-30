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

        el.addEventListener('input', function () {
            if (this.value.includes('aa')) {
                this.nextElementSibling.classList.add('mona')
            } else {
                this.nextElementSibling.classList.remove('mona')
            }
        })
    });
});