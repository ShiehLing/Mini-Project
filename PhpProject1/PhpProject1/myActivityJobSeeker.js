function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
  document.getElementById("main").style.marginLeft = "250px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
  document.getElementById("main").style.marginLeft = "0";
}

function showTab(tabName) {
    // Hide all tab content
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.style.display = 'none';
    });
    
    // Show the selected tab
    document.getElementById(tabName).style.display = 'block';
}

// Ensure that the "Bookmark" tab is shown by default when the page loads
document.addEventListener('DOMContentLoaded', function() {
    showTab('bookmark');  // Display the 'Bookmark' tab by default
});




