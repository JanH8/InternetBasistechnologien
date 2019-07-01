let alertState = false;
let delbtn = document.getElementById("del");
let delconf = document.getElementById("confirm");


function toggleDeleteAlert(){
    if(alertState){
        delconf.style.width="0";
        delconf.style.opacity="0";
        delbtn.innerHTML="Konto Deaktivieren";
        alertState = false;
    }
    else{
        delconf.style.width="100%";
        delconf.style.opacity="1";
        delbtn.innerHTML="Abbrechen";
        alertState = true;
    }
}