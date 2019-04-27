async function die() {
    document.getElementById("fbox").style.backgroundColor="green";
    document.getElementById("fbox").innerHTML="Logout Successfull";
    document.getElementById("fbox").style.opacity="0";

    document.getElementById("fbox2").innerHTML="You will be redirected!";
    document.getElementById("fbox2").style.backgroundColor="lightgray";
    document.getElementById("fbox2").style.opacity="0";
    await Sleep(5000);
    window.location.href="login.html"
}
function back() {
    window.location.href=document.referrer;
}

function Sleep(milliseconds) {
    return new Promise(resolve => setTimeout(resolve, milliseconds));
}

function forward() {
    window.location.href="index.html";
}