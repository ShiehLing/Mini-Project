/* When the user clicks on the button,
toggle between hiding and showing the dropdown content */
function myFunction() {
  document.getElementById("myDropdown").classList.toggle("show");
}

function openNav() {
    document.getElementById("mySidenav").style.width = "250px"; // Adjust width as needed
    document.getElementById("main").style.marginLeft = "250px"; // Push the main content to the right
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0"; // Collapse side nav
    document.getElementById("main").style.marginLeft = "0"; // Reset the margin
}



