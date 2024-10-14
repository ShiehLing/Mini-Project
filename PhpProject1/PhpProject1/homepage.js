//search button
document.addEventListener("DOMContentLoaded", function() {
    // Get the form element
    var form = document.querySelector('.search-form');

    // Create the button element
    var searchButton = document.createElement('button');
    searchButton.type = 'submit';
    searchButton.className = 'btn btn-success search-btn';
    searchButton.textContent = 'Search'; // Set button text

    // Append the button to the form
    form.appendChild(searchButton);
});

