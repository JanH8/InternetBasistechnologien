let shownav = false;

function togglenav() {
    if (shownav){
        document.getElementById("navc").style.left="50px";
        document.getElementById("navbar").style.left="-100%";
        document.getElementById("close").style.display="none";
        document.getElementById("navc").style.backgroundColor="white";
        document.getElementById("navbtn").style.color="blue";
        document.getElementById("navc").style.borderColor="blue";
        document.getElementById("navbtn").innerHTML="view_headline";
        document.getElementById("close").style.opacity="0";


        shownav = false;
    } else{
        document.getElementById("navc").style.left="500px";
        document.getElementById("navbar").style.left="0";
        document.getElementById("close").style.display="block";
        document.getElementById("navbtn").style.color="red";
        document.getElementById("navc").style.borderColor="red";
        document.getElementById("navbtn").innerHTML="clear";
        document.getElementById("close").style.opacity="0.4";
        shownav = true;
    }
}