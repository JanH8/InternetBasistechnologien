function showdummy(event, id) {
    let i, tab, tabcontent;

    tabcontent = document.getElementsByClassName("content-msg");
    for(i = 0; i<tabcontent.length;i++){
        tabcontent[i].style.display="none";
    }
    tab = document.getElementsByClassName("tab");
    for (i = 0; i<tab.length;i++){
        tab[i].className = tab[i].className.replace(" active", "");
    }
    document.getElementById(id).style.display="block";
    event.currentTarget.className += " active";
}