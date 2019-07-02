let shownav = false;
let btn = document.getElementById("navbtn");
let bar = document.getElementById("navbar");
let lg = document.getElementById("btnlogo");
let modal = document.getElementById("modal");
//let blocker = document.getElementById("navblock");

function togglenav() {
    if(shownav){
        btn.style.left="15px";
        btn.style.border="2px solid blue";
        btn.style.color="blue";
        lg.innerHTML="reorder";
        bar.style.left="-1000px";
        //blocker.style.left="-10000px";
        shownav = false;
        modal.style.display="none";
    }
    else{
        btn.style.left="450px";
        btn.style.border="2px solid red";
        btn.style.color="red";
        lg.innerHTML="clear";
        bar.style.left="0";
        //blocker.style.left="0";
        shownav = true;
        modal.style.display="block";
    }
}