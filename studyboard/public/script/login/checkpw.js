var username = document.getElementById("Username");
var password = document.getElementById("Password");
var alert = document.getElementById("alert");

function checkpw(){
    if (password.value.length <= 8){
        alert.innerHTML="Passwort zu kurz!";
        alert.style.opacity="1";
    } else{
        alert.style.opacity="0";
        checkusername();
    }
}

function checkusername() {
    if (username.value.length <=5){
        alert.innerHTML="Username zu kurz!";
        alert.style.opacity="1";
    } else{
        alert.style.opacity="0;";
        checkpw();
    }
}