let toggle_input;
function toggleinput(toggle_input){
    if(toggle_input){
        document.getElementById("showinput").style.bottom="0";
        document.getElementById("inputfield").style.bottom="-100%";
    }
    else{
        document.getElementById("showinput").style.bottom="-200%";
        document.getElementById("inputfield").style.bottom="0";

    }
}