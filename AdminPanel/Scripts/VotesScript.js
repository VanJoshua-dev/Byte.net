document.addEventListener("DOMContentLoaded", function () {
    console.log("Script loaded! in vote tab");
    let tab = document.querySelector("dashboard"); // Get the sidebar
    
});
function toggleProfile() {
    console.log('Toggling profile');
    let profile = document.querySelector("#profile"); // Get the sidebar
    profile.style.display = (profile.style.display === "none" || profile.style.display === "") ? "flex" : "none";
}