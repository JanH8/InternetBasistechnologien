let state = false;
let x = window.matchMedia("(max-width: 700px)");
function tooglecontactmenu(x) {
    if (x.matches) {
        if (state) {
            document.getElementById("contmen").style.height = "0";
            document.getElementById("contmen").style.width = "0";
            state = false;
        } else {
            document.getElementById("contmen").style.height = "90%";
            document.getElementById("contmen").style.width = "100%";
            state = true;
        }
    }else{
        if (state) {
            document.getElementById("contmen").style.height = "0";
            document.getElementById("contmen").style.width = "0";
            state = false;
        } else {
            document.getElementById("contmen").style.height = "75%";
            document.getElementById("contmen").style.width = "50%";
            state = true;
        }
    }
}