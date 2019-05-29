function scrollt(direction) {
    if(direction === 'forw'){
        document.getElementById("tabbar").scrollBy({left: 300, behavior: "smooth"});
    }
    else{
        document.getElementById("tabbar").scrollBy({left: -300, behavior: "smooth"})
    }
}