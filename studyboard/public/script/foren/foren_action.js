var currentForum = document.getElementById("forumIframe").src;
function changeCurrentForum(event, forumUrl) {
    document.location.href= "/forum/"+forumUrl;
}

function sendPostToForum() {
    if (currentForum) {
        var message = document.getElementById("chatin").value;
        var regex = new RegExp('forumTable', 'i');
        var forumApiUrl = currentForum.replace(regex, 'forumApi');
        console.log(forumApiUrl);
        var ajax = new XMLHttpRequest();
        ajax.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("chatin").value = "";
            }
        };
        ajax.open('POST', forumApiUrl);
        ajax.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        ajax.send('&message=' + message );
    }
}