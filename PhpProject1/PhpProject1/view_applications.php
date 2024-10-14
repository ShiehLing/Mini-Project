<?php
// Start the session and connect to the database
session_start();

// Database connection variables
$host = 'localhost'; // your host
$user = 'root';      // your database username
$password = '';      // your database password
$database = 'job_portal'; // your database name

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applications</title>
    <link rel="stylesheet" href="view_applications.css">
    <link href="https://fonts.googleapis.com/css2?family=YourFont:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="view_applications.js"></script>
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
        <a href="JobsPosted.php">Posted Jobs</a>
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
    
    








</html>


<?php


// Create a database connection
$conn = new mysqli($host, $user, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the employer is logged in
if (!isset($_SESSION['employer_id'])) {
    echo "You must be logged in as an employer to view applications.";
    exit();
}

// Get the employer ID from the session
$employer_id = $_SESSION['employer_id'];

// Set search filters (default empty)
$job_name_filter = isset($_GET['job_name']) ? $_GET['job_name'] : '';


// Process form submissions (Accept or Reject buttons)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = $_POST['application_id'];
    $action = $_POST['action']; // This will be either 'accept' or 'reject'

    // Update the status of the job application
    if ($action === 'accept') {
        $status = 'accepted';
    } elseif ($action === 'reject') {
        $status = 'rejected';
    }

    // Update the application status in the database
    $update_query = "UPDATE job_application SET Status = ?, Reviewed_At = NOW() WHERE A_ID = ? AND E_ID = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('sii', $status, $application_id, $employer_id);
    $stmt->execute();
    $stmt->close();
}

// Query to retrieve all pending job applications for jobs posted by the logged-in employer
$query = "
    SELECT
        job_application.A_ID,
        job_seeker.S_Name,
        job_seeker.Gender,
        job_seeker.Age,
        job_seeker.S_PhoneNo,
        job_seeker.S_Email,
        job_seeker.Address,
        job_seeker.AboutMe,
        resume.Detail AS Resume_Detail,
        job_application.Status,
        job_application.Application_Date,
        job_listing.Job_Name,
        job_listing.Job_Type,
        employer.Location,
        category.C_Name AS Category
    FROM
        job_application
    INNER JOIN job_seeker ON job_application.S_ID = job_seeker.S_ID
    INNER JOIN resume ON job_application.R_ID = resume.R_ID
    INNER JOIN job_listing ON job_application.L_ID = job_listing.L_ID
    INNER JOIN employer ON job_listing.E_ID = employer.E_ID  -- Adjust this line
    INNER JOIN category ON job_listing.C_ID = category.C_ID
    WHERE
        job_application.E_ID = ? AND job_application.Status = 'pending'
";



// Add filters to the query dynamically based on input
$filter_params = [$employer_id];
$filter_types = 'i';

if (!empty($job_name_filter)) {
    $query .= " AND job_listing.Job_Name LIKE ?";
    $filter_params[] = '%' . $job_name_filter . '%';
    $filter_types .= 's';
}



// Prepare the SQL statement
$stmt = $conn->prepare($query);
$stmt->bind_param($filter_types, ...$filter_params);
$stmt->execute();
$result = $stmt->get_result();

?>



    
     <div id="main" class="activity-section">
         
         
        <div id="search-container" class="search-container">
            <form method="GET" action="" class="search-form">
                <label for="job_name">Job Name:</label>
                <input type="text" name="job_name" class="form-control" value="<?= htmlspecialchars($job_name_filter) ?>">

                

                <button type="submit" class="btn btn-primary mt-2">Search</button>
            </form>
        </div>
         
    <div class="application-container">
    <?php

    // Display pending applications
    if ($result->num_rows > 0) {
        // Loop through each application and display it in a form format
        while ($row = $result->fetch_assoc()) {
            echo "<form action='' method='post' class='application-form'>
                <h3>Job Application Details</h3>

                <label for='name'><strong>Applicant Name:</strong></label>
                <input type='text' id='name' name='name' value='{$row['S_Name']}' readonly><br><br>

                <label for='gender'><strong>Gender:</strong></label>
                <input type='text' id='gender' name='gender' value='{$row['Gender']}' readonly><br><br>

                <label for='age'><strong>Age:</strong></label>
                <input type='text' id='age' name='age' value='{$row['Age']}' readonly><br><br>

                <label for='phone'><strong>Phone Number:</strong></label>
                <input type='text' id='phone' name='phone' value='{$row['S_PhoneNo']}' readonly><br><br>

                <label for='email'><strong>Email:</strong></label>
                <input type='email' id='email' name='email' value='{$row['S_Email']}' readonly><br><br>

                <label for='address'><strong>Address:</strong></label>
                <input type='text' id='address' name='address' value='{$row['Address']}' readonly><br><br>

                <label for='about'><strong>About Me:</strong></label>
                <textarea id='about' name='about' rows='4' cols='50' readonly>{$row['AboutMe']}</textarea><br><br>

                <label for='resume'><strong>Resume:</strong></label>
                <textarea id='resume' name='resume' rows='4' cols='50' readonly>{$row['Resume_Detail']}</textarea><br><br>

                <label for='date'><strong>Application Date:</strong></label>
                <input type='text' id='date' name='date' value='{$row['Application_Date']}' readonly><br><br>

                <label for='job'><strong>Job Applied For:</strong></label>
                <input type='text' id='job' name='job' value='{$row['Job_Name']}' readonly><br><br>

                <!-- Hidden field to hold the application ID -->
                <input type='hidden' name='application_id' value='{$row['A_ID']}'>


                <!-- Accept and Reject buttons -->
                <input type='submit' name='action' value='accept' class='btn-accept'>
                <input type='submit' name='action' value='reject' class='btn-reject'>
            </form>";
        }
    } else {
        echo "<b>No pending applications found for your job listings.</b>";
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
    ?>
    </div>
</div>
    
   