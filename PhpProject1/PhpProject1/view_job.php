<?php
session_start(); // Start the session

// Database connection
$host = 'localhost';
$db = 'job_portal';
$user = 'root'; // Adjust based on your setup
$pass = ''; // Adjust based on your setup

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if employer ID is set in session
if (!isset($_SESSION['employer_id'])) {
    echo '<script>
        alert("Employer ID is not set in session.");
        window.location.href = "login.php";  // Redirect to login page
    </script>';
    exit();
}

// Check if job ID (L_ID) is provided
if (!isset($_GET['L_ID'])) {
    echo '<script>
        alert("No job ID provided.");
        window.location.href = "MyActivityEmployer.php";  // Redirect to posted jobs
    </script>';
    exit();
}

// Get the job ID from the URL
$job_id = $_GET['L_ID'];

// Prepare SQL query to fetch job details
$sql = "SELECT jl.L_ID, jl.Job_Name, jl.Job_Type, jl.Salary, 
               jl.Job_Responsibilities, jl.Requirement, jl.Add_On, 
               c.C_Name AS Category, e.E_Name AS Employer_Name 
        FROM job_listing jl
        INNER JOIN category c ON jl.C_ID = c.C_ID
        INNER JOIN employer e ON jl.E_ID = e.E_ID
        WHERE jl.L_ID = ? AND jl.E_ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $job_id, $_SESSION['employer_id']); // Bind parameters
$stmt->execute();
$result = $stmt->get_result();

// Fetch job details
if ($result && $result->num_rows > 0) {
    $job_details = $result->fetch_assoc();
} else {
    echo '<script>
        alert("No job found or you do not have permission to view this job.");
        window.location.href = "MyActivityEmployer.php";  // Redirect to posted jobs
    </script>';
    exit();
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($job_details['Job_Name']); ?> - Job Details</title>
    <link rel="stylesheet" href="view_job.css?v=1">
    <link href="https://fonts.googleapis.com/css2?family=YourFont:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</head>
<body>
    <header class="d-flex align-items-center justify-content-between p-3">
        <div class="d-flex align-items-center">
            <img src="TT_logo.png" alt="Talent Trove Logo" class="logo me-2">
            <a class="site-name" href="index.php"><h1>View Posted Jobs</h1></a>
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

    
    <!-- Job Name (Centered Above the Box) -->
<h2 class="job-title"><?php echo htmlspecialchars($job_details['Job_Name']); ?></h2>

<div class="job-details-container">
    <div class="info-item">
        <strong>Employer:</strong>
        <span><?php echo htmlspecialchars($job_details['Employer_Name']); ?></span>
    </div>
    <div class="info-item">
        <strong>Category:</strong>
        <span><?php echo htmlspecialchars($job_details['Category']); ?></span>
    </div>
    <div class="info-item">
        <strong>Job Type:</strong>
        <span><?php echo htmlspecialchars($job_details['Job_Type']); ?></span>
    </div>
    <div class="info-item">
        <strong>Salary:</strong>
        <span><?php echo htmlspecialchars($job_details['Salary']); ?></span>
    </div>
    <div class="info-item">
        <strong>Responsibilities:</strong>
        <span><?php echo nl2br(htmlspecialchars($job_details['Job_Responsibilities'])); ?></span>
    </div>
    <div class="info-item">
        <strong>Requirements:</strong>
        <span><?php echo nl2br(htmlspecialchars($job_details['Requirement'])); ?></span>
    </div>
    <div class="info-item">
        <strong>Additional Information:</strong>
        <span><?php echo nl2br(htmlspecialchars($job_details['Add_On'])); ?></span>
    </div>
</div>


<div class="back-btn-container">
    <a href="JobsPosted.php" class="btn-primary">Back to Posted Jobs</a>
    <a href="EmployerActivity.php" class="btn-primary">Go to Employer Activity</a> <!-- New Button -->
</div>


<br>
<br>

    
    
</body>
</html>

