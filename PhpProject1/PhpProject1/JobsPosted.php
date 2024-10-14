<?php
session_start(); // Ensure this is at the top of your PHP file

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    echo 
    //alert
    '<script>
    alert("Employer ID is not set in session.");
    window.location.href = "login.php";  // Redirect to login page
    </script>';
    
    exit(); // Stop further execution if employer ID is not set
} else {
    $employer_id = $_SESSION['employer_id'];
}

// Initialize job posts array
$job_posts = [];

// Fetch job listings for this employer (only available jobs)
$sql = "SELECT job_listing.L_ID, job_listing.Job_Name AS Title, category.C_Name AS Category, 
        job_listing.Salary, job_listing.Job_Type, job_listing.Created_at 
        FROM job_listing
        INNER JOIN category ON job_listing.C_ID = category.C_ID
        WHERE job_listing.E_ID = ? AND job_listing.status = 'available'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employer_id); // Bind the employer ID parameter
$stmt->execute();
$result = $stmt->get_result();

// Check for errors in the query and populate $job_posts
if ($result && $result->num_rows > 0) {
    $job_posts = $result->fetch_all(MYSQLI_ASSOC);
}

// Check if job ID is set for removal (updating status)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_job_id'])) {
    $remove_job_id = $_POST['remove_job_id'];

    // Prepare a statement to update the job status to 'unavailable'
    $stmt = $conn->prepare("UPDATE job_listing SET status = 'unavailable' WHERE L_ID = ? AND E_ID = ?");
    $stmt->bind_param("ii", $remove_job_id, $employer_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo 
            '<script>
            alert("Job removed successfully.");
            window.location.href = "JobsPosted.php";  // Redirect to the activity page
            </script>';
        } else {
            echo 
            '<script>
            alert("Job not found or already removed.");
            window.location.href = "JobsPosted.php";  // Redirect to the activity page
            </script>';
        }
    } else {
        echo 
        '<script>
        alert("Error removing job: ' . $stmt->error . '");
        window.location.href = "MyActivityEmployer.php";  // Redirect to the activity page
        </script>'; 
    }

    $stmt->close();
}


// Fetch applications for jobs posted by the employer
$sql_applications = "
SELECT 
    ja.A_ID, 
    jl.Job_Name, 
    jl.Job_Type, 
    jl.Salary, 
    c.C_Name AS Job_Category, 
    ja.Application_Date, 
    ja.Status 
FROM 
    job_application ja
JOIN 
    job_listing jl ON ja.L_ID = jl.L_ID
JOIN 
    category c ON jl.C_ID = c.C_ID
WHERE 
    jl.E_ID = ?";  // Filtering by the employer ID in the job_listing table

// Bind employer ID (only), and fetch applications
$stmt_applications = $conn->prepare($sql_applications);
$stmt_applications->bind_param("i", $employer_id); // Only binding employer_id now
$stmt_applications->execute();
$result_applications = $stmt_applications->get_result();

// Fetch the results
if ($result_applications && $result_applications->num_rows > 0) {
    $applications = $result_applications->fetch_all(MYSQLI_ASSOC);
} else {
    $applications = []; // Initialize if no applications found
}

if ($result_applications) {
    $applications = $result_applications->fetch_all(MYSQLI_ASSOC);
    error_log("Number of applications: " . count($applications)); // Debugging line
} else {
    error_log("No applications found for the query: " . $stmt_applications->error);
    $applications = [];
}

// Check if the application status is being updated
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'])) {
    $application_id = $_POST['application_id'];
    $status = $_POST['status'];

    // Update application status
    $stmt = $conn->prepare("UPDATE job_application SET Status = ? WHERE A_ID = ?");
    $stmt->bind_param("si", $status, $application_id);

    if ($stmt->execute()) {
        echo '<script>alert("Application status updated successfully.");</script>';
        // Optionally, redirect or refresh the page
    } else {
        echo '<script>alert("Error updating application status: ' . $stmt->error . '");</script>';
    }
}

$conn->close();
?>

<!-- Rest of your HTML -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jobs Posted</title>
    <link rel="stylesheet" href="JobsPosted.css?v=1">
    <link href="https://fonts.googleapis.com/css2?family=YourFont:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="JobsPosted.js"></script>
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

    <!-- Main Content -->
    <div id="main" class="activity-section">
        <h2>Jobs Posted</h2>
    

    <!-- Jobs Posted Section -->
    <div id="jobs-posted" class="tab-content">
        <?php if (!empty($job_posts)): ?>
            <?php foreach ($job_posts as $row): ?>
                <div class="job-post border p-3 mb-3">
                    <div class="job-info">
                        <p><strong>Title:</strong> <?php echo htmlspecialchars($row['Title']); ?></p>
                        <p><strong>Type:</strong> <?php echo htmlspecialchars($row['Job_Type']); ?></p>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($row['Category']); ?></p>
                        <p><strong>Salary:</strong> <?php echo htmlspecialchars($row['Salary']); ?></p>
                        <p><strong>Posted:</strong> <?php echo date('d M Y', strtotime($row['Created_at'])); ?></p>
                    </div>
                    <div class="job-actions mt-3">
                        <a href="view_job.php?L_ID=<?php echo $row['L_ID']; ?>" class="btn btn-info me-2">View</a>
                        <button onclick="editJob(<?php echo $row['L_ID']; ?>)" class="btn btn-warning me-2">Edit</button>
                        
                        <!-- Remove Button -->
                        <form method="POST" onsubmit="return confirm('Are you sure you want to remove this job?');" style="display:inline;">
                            <input type="hidden" name="remove_job_id" value="<?php echo $row['L_ID']; ?>">
                            <button type="submit" class="btn btn-danger">Unavailable</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No jobs posted yet.</p>
        <?php endif; ?>
    </div>
    
    </div>
    
    
    
</body>
</html>

