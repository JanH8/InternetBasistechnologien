let logoutalert = false;
function togglelogoutalert(){
    if(logoutalert){
        document.getElementById("dblock").style.height="0";
        logoutalert = false;
    }
    else{
        document.getElementById("dblock").style.height="110%";
        logoutalert = true;
    }
}