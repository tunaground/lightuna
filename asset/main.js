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
