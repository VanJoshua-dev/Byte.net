console.log("ScriptLoadied in admin page")

document.addEventListener("DOMContentLoaded", function () {
    console.log("Script loaded!");
    let tab = document.querySelector("dashboard"); // Get the sidebar
    
});

// Prevents the page from reloading
document.querySelector("#profile-form").addEventListener("submit", function(event) {
    event.preventDefault(); 
});
//open sidebar for mobile
function toggleSlidebar() {
    console.log('Open slide bar');
    let sidebar = document.querySelector(".slide-bar"); // Get the sidebar
    sidebar.style.left = "0"; // Move it to the left (visible)
}
//open sidebar for mobile
function close_slidebar() {
    console.log('Close slide bar');
    let sidebar = document.querySelector(".slide-bar"); // Get the sidebar
    sidebar.style.left = "-500px"; // Move it to the left (visible)
}
function toogleDropdown(){
    let dropdown = document.querySelector(".dropdown");
    let btn = document.querySelector("#dropdownbtn");

    // Check if dropdown is currently visible
    let isVisible = dropdown.style.display === "flex";

    if (isVisible) {
        dropdown.style.display = "none";
        dropdown.style.opacity = 0;
        btn.style.transform = "rotate(0deg)";
    } else {
        dropdown.style.display = "flex";
        dropdown.style.opacity = 1;
        btn.style.transform = "rotate(-180deg)";
    }
   
}

//print voting tally
function printVoteTally(elementId){
    var element = document.getElementById(elementId);
    var printWindow = window.open('', '', 'width=600,height=400');
    printWindow.document.write('<html><head><title>Print</title></head><body>');
    printWindow.document.write(element.outerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}


//return to GoToLogin
function returntoLogin(){
    console.log('Return to login page');
    window.location.href = "/Byte.net/vote/logout.php";
}
