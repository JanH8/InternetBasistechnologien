let windowstate = false;
let hlpWindow = document.getElementById("helpWindow")

function toggleHelpWindow(){
    if(windowstate){
        hlpWindow.style.display="none";
        windowstate = false;
    }
    else{
        hlpWindow.style.display="block";
        windowstate = true;
    }
}