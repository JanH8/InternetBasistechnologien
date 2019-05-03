let state = false;
function tooglecontactmenu() {
    if(state){
        document.getElementById("contmen").style.height="0";
        document.getElementById("contmen").style.width="0";
        state = false;
    }
    else{
        document.getElementById("contmen").style.height="75%";
        document.getElementById("contmen").style.width="50%";
        state = true;
    }
}