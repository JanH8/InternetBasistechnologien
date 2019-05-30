function opensite(event, page){
    sites = document.getElementsByClassName("content");
    for (var i = 0; i < sites.length; i++){

        sites[i].style.display="none";
    }

    links = document.getElementsByClassName('barlink');
    for(var i = 0; i< links.length; i++){
        links[i].className = links[i].className.replace(" active", " ");
    }

    document.getElementById(page).style.display="block";
    event.currentTarget.className += " active";
}