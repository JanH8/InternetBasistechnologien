function deletePost(forumId, postId) {
    const Http = new XMLHttpRequest();
    const url='/deletePost/'+forumId+'/'+postId;
    Http.open("GET", url);
    Http.send();

    Http.onreadystatechange = (e) => {
        location.reload(true);
    }
}

function newAbo(forumId) {
    const Http = new XMLHttpRequest();
    const url='/newAbo/'+forumId;
    Http.open("GET", url);
    Http.send();

    Http.onreadystatechange = (e) => {
        location.reload(true);
    }
}

function removeAbo(forumId) {
    const Http = new XMLHttpRequest();
    const url='/deleteAbo/'+forumId;
    Http.open("GET", url);
    Http.send();

    Http.onreadystatechange = (e) => {
        location.reload(true);
    }
}

function deactivateForum(forumId) {
    const Http = new XMLHttpRequest();
    const url='/deactivateForum/'+forumId;
    Http.open("GET", url);
    Http.send();

    Http.onreadystatechange = (e) => {
window.top.location.reload();
    }
}