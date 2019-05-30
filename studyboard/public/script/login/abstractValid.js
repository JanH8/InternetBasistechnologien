//script for field validation on user creation

function validateEmail(fieldID, alertID, alertText) {
    tmp = document.getElementById(fieldID).value;
    alert = document.getElementById(alertID);

    if (tmp.includes('@') && tmp.includes('.') && tmp.length >=  5){
        alert.style.opacity="0";
    }
    else{
        alert.style.opacity="1";
        alert.innerHTML=alertText;
    }
}
function validatePassword(fieldID, alertID, alertText) {
    tmp = document.getElementById(fieldID).value;
    alert = document.getElementById(alertID);

    if (tmp.length >=  5){
        alert.style.opacity="0";
    }
    else{
        alert.style.opacity="1";
        alert.innerHTML=alertText;
    }
}
function validateIdent(field1, field2, alertID, alertText){
    tmp1 = document.getElementById(field1).value;
    tmp2 = document.getElementById(field2).value;
    alert = document.getElementById(alertID);

    if(tmp1 === tmp2){
        alert.style.opacity="0";
    }
    else{
        alert.style.opacity="1";
        alert.innerHTML=alertText;
    }
}
