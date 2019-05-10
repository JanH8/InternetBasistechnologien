var currentForum = "";

function changeCurrentForum(event, forumUrl) {
    currentForum = forumUrl;
    document.getElementById("forumIframe").src = "/forum/" + currentForum;
}

function sendPostToForum() {
    if (currentForum) {
        var message = document.getElementById("chatin").value;
        var forumApiUrl = "/forumApi/" + currentForum;
        var ajax = new XMLHttpRequest();
        ajax.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("chatin").value = "";
            }
        };
        ajax.open('POST', forumApiUrl);
        ajax.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        ajax.send('&message=' + message);

    }
}