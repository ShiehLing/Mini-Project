<?php
// Start the session and connect to the database
session_start();

// Database connection variables
$host = 'localhost'; // your host
$user = 'root';      // your database username
$password = '';      // your database password
$database = 'job_portal'; // your database name

// Create a database connection
$conn = new mysqli($host, $user, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the employer is logged in
if (!isset($_SESSION['employer_id'])) {
    echo "You must be logged in as an employer to view application history.";
    exit();
}

// Get the employer ID from the session
$employer_id = $_SESSION['employer_id'];

// Set search filters (default empty)
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$job_name_filter = isset($_GET['job_name']) ? $_GET['job_name'] : '';
$location_filter = isset($_GET['location']) ? $_GET['location'] : '';

// Query to retrieve accepted and rejected job applications
$query = "
    SELECT
        job_application.A_ID,
        job_seeker.S_Name,
        job_application.Status,
        job_application.Reviewed_At,
        job_listing.Job_Name,
        category.C_Name AS Category,
        employer.Location
    FROM
        job_application
    INNER JOIN job_seeker ON job_application.S_ID = job_seeker.S_ID
    INNER JOIN job_listing ON job_application.L_ID = job_listing.L_ID
    INNER JOIN category ON job_listing.C_ID = category.C_ID
    INNER JOIN employer ON job_application.E_ID = employer.E_ID
    WHERE
        job_application.E_ID = ? AND job_application.Status IN ('accepted', 'rejected')
";

// Add filters to the query dynamically based on input
if (!empty($category_filter)) {
    $query .= " AND category.C_Name = ?";
}
if (!empty($status_filter)) {
    $query .= " AND job_application.Status = ?";
}
if (!empty($job_name_filter)) {
    $query .= " AND job_listing.Job_Name LIKE ?";
}
if (!empty($location_filter)) {
    $query .= " AND employer.Location LIKE ?";
}

// Prepare the SQL statement
$stmt = $conn->prepare($query);

// Bind parameters dynamically based on filters
$types = 'i'; // initial 'i' for employer_id
$params = [$employer_id];

if (!empty($category_filter)) {
    $types .= 's';
    $params[] = $category_filter;
}
if (!empty($status_filter)) {
    $types .= 's';
    $params[] = $status_filter;
}
if (!empty($job_name_filter)) {
    $types .= 's';
    $params[] = '%' . $job_name_filter . '%';
}
if (!empty($location_filter)) {
    $types .= 's';
    $params[] = '%' . $location_filter . '%';
}

// Use call_user_func_array for dynamic binding
$stmt->bind_param($types, ...$params);

// Execute the query and get the result
$stmt->execute();
$result = $stmt->get_result(); ?>

<!-- Main Content -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="employerProfile.js"></script>
    <link rel="stylesheet" href="application_history.css"/>
    
    <link href="https://fonts.googleapis.com/css2?family=YourFont:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <title>Employer Application History</title>
</head>
<body>
    <header class="d-flex align-items-center justify-content-between p-3">
        <div class="d-flex align-items-center">
            <img src="TT_logo.png" alt="Talent Trove Logo" class="logo me-2">
            <a class="site-name" href="index.php"><h1>Talent Trove</h1></a>
        </div>
        
        <nav class="d-flex justify-content-end">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Home</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="about_us.php">About Us</a></li>
                        <li><a class="dropdown-item" href="faq.php">FAQ</a></li>
                        <li><a class="dropdown-item" href="pp.php">Privacy & Policies</a></li>
                        <li><a class="dropdown-item" href="job_listing.php">Job Listing</a></li>
                        
                            
                        <hr class="custom-divider">
                        
                        <li><a class="dropdown-item" href="logout.php" id="logoutButton">Logout</a></li>
                    </ul>
                </li>
            </ul>
            
            <script>
                // Function to simulate user login
                function signInUser() {
                    sessionStorage.setItem('isLoggedIn', 'true'); // Set logged-in state
                    window.location.reload(); // Reload page to update the buttons
                }

                // Function to simulate user logout
                function logOutUser() {
                    sessionStorage.removeItem('isLoggedIn'); // Remove logged-in state
                    window.location.reload(); // Reload page to update the buttons
                }
            </script>

            
            <ul class="d-flex">
                <li class="nav-item">
                    <a href="login.php" class="btn btn-primary" id="loginButton">Sign In/Sign Up</a>
                </li>
            </ul>
        </nav>      
    </header>
    
    <!-- Side Navigation -->
    <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="EmployerActivity.php">Employer DashBoard</a>
        <a href="employerProfile.php">Employer Profile</a>
        <a href="postJob.php">Post A Job</a>
        <a href="JobsPosted.php">Jobs Posted</a>
        <a href="application_history.php">Application History</a>
        <a href="view_applications.php">View Application</a>
        
    </div>

    <!-- Custom button to open the side navigation -->
    <button class="btn" onclick="openNav()">
        <span class="icon">
            <svg viewBox="0 0 175 80" width="40" height="40">
            <rect width="80" height="15" fill="#f0f0f0" rx="10"></rect>
            <rect y="30" width="80" height="15" fill="#f0f0f0" rx="10"></rect>
            <rect y="60" width="80" height="15" fill="#f0f0f0" rx="10"></rect>
            </svg>
        </span>
        <span class="text">MENU</span>
    </button>


    <div id="main" class="activity-section">
<div id="search-container" class="search-container">
    <form method="GET" action="" class="search-form">
        <label for="category">Category:</label>
        <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($category_filter) ?>">

        <label for="status">Status:</label>
        <select name="status" class="form-select">
            <option value="">All</option>
            <option value="accepted" <?= $status_filter == 'accepted' ? 'selected' : '' ?>>Accepted</option>
            <option value="rejected" <?= $status_filter == 'rejected' ? 'selected' : '' ?>>Rejected</option>
        </select>

        <label for="job_name">Job Name:</label>
        <input type="text" name="job_name" class="form-control" value="<?= htmlspecialchars($job_name_filter) ?>">

        <label for="location">Location:</label>
        <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($location_filter) ?>">

        <button type="submit" class="search-btn">Search</button>
    </form>
</div>



<?php
// Display accepted and rejected applications
if ($result->num_rows > 0) {
    echo "<h3>Application History</h3>";
    echo "<table border='1'>
        <tr>
            <th>Applicant Name</th>
            <th>Job Applied For</th>
            <th>Category</th>
            <th>Location</th>
            <th>Status</th>
            <th>Reviewed At</th>
        </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['S_Name']}</td>
            <td>{$row['Job_Name']}</td>
            <td>{$row['Category']}</td>
            <td>{$row['Location']}</td>
            <td>{$row['Status']}</td>
            <td>{$row['Reviewed_At']}</td>
        </tr>";
    }

    echo "</table>";
} else {
    echo "<b>No accepted or rejected applications found.</b>";
}


// Close the database connection
$stmt->close();
$conn->close();
?>
    </div>
   