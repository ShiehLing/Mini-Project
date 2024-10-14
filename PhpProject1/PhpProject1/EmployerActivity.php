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

// Check if the employer is logged in
if (!isset($_SESSION['employer_id'])) {
    // If not logged in, redirect to the login page 
    echo "Please log in as an employer to view your activity.";
    header("Location: login.php");
    exit;
}

// Get employer ID from session
$employer_id = $_SESSION['employer_id'];

// Track job views
if (isset($_GET['job_id'])) {
    $job_id = $_GET['job_id'];  // Assume the job ID is passed as a query parameter

    // Increment view count
    $query = "UPDATE job_listing SET view_count = IFNULL(view_count, 0) + 1 WHERE L_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
}

$query = "
  SELECT jl.L_ID, jl.Job_Name, jl.view_count, COUNT(ja.A_ID) as application_count 
  FROM job_listing jl
  LEFT JOIN job_application ja ON jl.L_ID = ja.L_ID
  WHERE jl.E_ID = ? AND jl.status = 'available'
  GROUP BY jl.L_ID";


$stmt = $conn->prepare($query);
$stmt->bind_param("i", $employer_id);
$stmt->execute();
$result = $stmt->get_result();
$jobs = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer DashBoard</title>
    <link rel="stylesheet" href="EmployerActivity.css?v=1">
    <link href="https://fonts.googleapis.com/css2?family=YourFont:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    <body>
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

    
<h1>Employer Dashboard</h1>

<table>
    <thead>
        <tr>
            <th>Job Name</th>
            <th>Views</th>
            <th>Applications</th>
            <th>Action</th> <!-- New column for the button -->
        </tr>
    </thead>
    <tbody>
        <?php foreach ($jobs as $job): ?>
            <tr>
                <td><?= htmlspecialchars($job['Job_Name']) ?></td>
                <td><?= htmlspecialchars($job['view_count']) ?></td>
                <td><?= htmlspecialchars($job['application_count']) ?></td>
                
                <td>
                    <div class="button-container"> <!-- Wrap the button in a div -->
                        <a href="view_job.php?L_ID=<?= htmlspecialchars($job['L_ID']) ?>" class="btn-view">View</a> <!-- Changed parameter to 'L_ID' -->
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


    <!-- Charts section -->
   <div class="chart-container">
    <h2 class="chart-title"> Chart for Applications and Views</h2> <!-- Title for the charts -->
    <div class="chart-box"> <!-- Box to style the charts -->
        <p class="chart-title">Job Applications</p> <!-- Title for applications chart -->
        <canvas id="jobApplicationsChart"></canvas> <!-- Applications Chart -->
        
        <p class="chart-title">Job Views</p> <!-- Title for views chart -->
        <canvas id="jobViewsChart"></canvas> <!-- Views Chart -->
    </div>
</div>
    
        
     </div>
</body>
</html>

<script>
    function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
    document.getElementById("main").style.marginLeft = "250px";
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
    document.getElementById("main").style.marginLeft = "0";
}
    
    // Sample data for jobs
const jobNames = <?= json_encode(array_column($jobs, 'Job_Name')) ?>;
const applicationCounts = <?= json_encode(array_column($jobs, 'application_count')) ?>;
const viewCounts = <?= json_encode(array_column($jobs, 'view_count')) ?>;


    // Applications chart
    const appCtx = document.getElementById('jobApplicationsChart').getContext('2d');
    const applicationsChart = new Chart(appCtx, {
        type: 'bar',
        data: {
            labels: jobNames,
            datasets: [{
                label: 'Applications',
                data: applicationCounts,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Views chart
    const viewCtx = document.getElementById('jobViewsChart').getContext('2d');
    const viewsChart = new Chart(viewCtx, {
        type: 'bar',
        data: {
            labels: jobNames,
            datasets: [{
                label: 'Views',
                data: viewCounts,
                backgroundColor: 'rgba(153, 102, 255, 0.6)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    
    
  

</script>



