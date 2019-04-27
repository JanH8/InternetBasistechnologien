const alert = false;
document.getElementById("alertmsg").innerHTML="Hier steht ihre Nachricht";

if(alert === false){
    document.getElementById("alert").style.display="none";
}

function closealert() {
    document.getElementById("alert").style.top="-100%";
}