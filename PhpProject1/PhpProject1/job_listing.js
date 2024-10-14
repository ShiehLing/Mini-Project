document.addEventListener("DOMContentLoaded", function() {
    // The usersRole is set dynamically from PHP
    var usersRole = window.usersRole;  // 'job_seeker' or 'guest'

    // Find all job items and dynamically add buttons based on the user role
    document.querySelectorAll('.job-item').forEach(function(jobItem) {
        var listingId = jobItem.getAttribute('data-listing-id');  // Get the listing ID from the data attribute
    });
});
