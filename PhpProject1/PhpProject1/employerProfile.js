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

function previewImage(event) {
    var imagePreview = document.getElementById('imagePreview');
    var reader = new FileReader();
    
    reader.onload = function(){
        imagePreview.src = reader.result;
        imagePreview.style.display = 'block'; // Show the image preview
    }
    
    if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}