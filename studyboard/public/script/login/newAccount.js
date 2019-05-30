//v3 TODO Redundanzen entfernen
/*
uservalidation()            checks length of username
emailvalidation()           checks if string contains @
passwordvalidation()        checks if pw is long enough
identityvalidation()        checks if passowrds are the same
 */

var userstate = false;
var emailstate = false;
var passwordstate = false;
var validstate = false;



function userValidation() {
    console.log("Starte Nutzer Validierung");

    var field = document.getElementById('Username').value;
    var alert = document.getElementById('usrAlert');

    if(field.length <= 5){
        alert.innerHTML="Benutzername zu kurz!";
        alert.style.opacity="1";
        userstate = false;
    }
    else if(field.length >= 20){
        alert.innerHTML="Benutzername zu lang!";
        alert.style.opacity="1";
        userstate=false;
    }
    else{
        alert.style.opacity="0";
        userstate = true;
    }
    showsubmit();
}

function emailValidation() {
    console.log("Starte E-Mail Validierung");

    var field = document.getElementById('email').value;
    var alert = document.getElementById('mailAlert');

    if(!field.includes("@") || !field.includes(".")){
        alert.innerHTML="Eingabe ist keine E-Mail!";
        alert.style.opacity="1";
        emailstate=false;
    }
    else if (field.length <= 3){
        alert.innerHTML="Eingabe zu kurz!";
        alert.style.opacity="1";
        emailstate=false;
    }
    else{
        alert.style.opacity="0";
        emailstate=true;

    }
    showsubmit();
}

function passwordValidation(){
    console.log("Starte Passwort validierung");

    var field = document.getElementById('Password').value;
    var alert = document.getElementById('pwAlert');

    if(field.length <= 8){
        alert.innerHTML="Passwort zu kurz!";
        alert.style.opacity="1";
        passwordstate = false;
    }
    else{
        alert.style.opacity="0";
        passwordstate = true;
        identityValidation();
    }

    identityValidation();
    showsubmit();
}

function identityValidation() {
    console.log("Starte gleicheits validierung");
    var tmp1 = document.getElementById('Password').value;
    var tmp2 = document.getElementById('PasswordConf').value;
    var alert = document.getElementById('validAlert');

    console.log(tmp1 + " " + tmp2)
    if(tmp1 === tmp2){
        alert.style.opacity="0";
        validstate = true;
    }
    else{
        alert.innerHTML="Passwörter stimmen nicht überein!";
        alert.style.opacity="1";
        validstate = false;
    }
    showsubmit();
}

function showsubmit(){
    var btn = document.getElementById('submit');
    if(userstate && emailstate && passwordstate && validstate){
        console.log("ALLE WERTE GLEICH!");
        btn.style.display="block";
    }
    else{
        btn.style.display="none";
    }
}

function backLog() {
    window.location="/";
}