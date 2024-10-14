// Sidenav Functions
function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
    document.getElementById("main").style.marginLeft = "270px";
    document.body.classList.add('sidenav-active');
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
    document.getElementById("main").style.marginLeft = "0"; 
    document.body.classList.remove('sidenav-active');
}

// Profile Image Preview without saving instantly
document.getElementById('imageUpload').addEventListener('change', function(event) {
    var file = event.target.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profileImage').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});

// Profile Image Click Event - Opens the file picker
document.getElementById('profileImage').addEventListener('click', function() {
    document.getElementById('imageUpload').click();
});

