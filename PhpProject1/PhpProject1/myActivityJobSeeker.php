<?php
session_start(); // Ensure this is at the top of your PHP file

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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch saved jobs
$query = "SELECT job_listing.Job_Name, job_listing.L_ID, job_listing.Salary, job_listing.Job_Type, employer.E_Name 
          FROM saved_jobs 
          JOIN job_listing ON saved_jobs.L_ID = job_listing.L_ID 
          JOIN employer ON job_listing.E_ID = employer.E_ID 
          WHERE saved_jobs.S_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$saved_jobs_result = $stmt->get_result();

// Fetch job applications
$applications_query = "
    SELECT job_application.A_ID, job_listing.Job_Name, job_application.Status, job_application.Application_Date 
    FROM job_application 
    JOIN job_listing ON job_application.L_ID = job_listing.L_ID 
    WHERE job_application.S_ID = ?";
$applications_stmt = $conn->prepare($applications_query);
$applications_stmt->bind_param('i', $user_id);
$applications_stmt->execute();
$applications_result = $applications_stmt->get_result();

$stmt->close();
$applications_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talent Trove - Unlock Your Potential</title>
    <link rel="stylesheet" href="myActivityJobSeeker.css?">
    <link href="https://fonts.googleapis.com/css2?family=YourFont:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="myActivityJobSeeker.js"></script>
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
                        <li><a class="dropdown-item" href="userProfile.php">User Profile</a></li>
                        <li><a class="dropdown-item" href="myActivityJobSeeker.php">My Activity</a></li>
                            
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
        <a href="UserProfile.php">User Profile</a>
        <a href="myActivityJobSeeker.php">Job Seeker Activity</a>
        <a href="jobSeekerHistory.php">Application History</a>
        <a href="job_listing.php">Job Listing</a>
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
        <h2>My Activity</h2>
        
        <div class="radio-inputs">
            <label class="radio">
                <input type="radio" name="activity" checked onclick="showTab('bookmark')" />
                <span class="name">Bookmark</span>
            </label>
            <label class="radio">
                <input type="radio" name="activity" onclick="showTab('applications')" />
                <span class="name">Applications</span>
            </label>
        </div>

        <div id="bookmark" class="tab-content">
            <!-- This is where your saved jobs should be displayed -->
            <?php if ($saved_jobs_result->num_rows > 0): ?>
                <?php while ($row = $saved_jobs_result->fetch_assoc()): ?>
                    <div class="saved-job">
                        <h2><?php echo htmlspecialchars($row['Job_Name']); ?></h2>
                        <p>Employer: <?php echo htmlspecialchars($row['E_Name']); ?></p>
                        <p>Type: <?php echo htmlspecialchars($row['Job_Type']); ?></p>
                        <p>Salary: <?php echo htmlspecialchars($row['Salary']); ?></p>
                        <a href="job_info.php?job_id=<?php echo $row['L_ID']; ?>">View Details</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>You have no saved jobs.</p>
            <?php endif; ?>
        </div>

        <div id="applications" class="tab-content" style="display: none;">
            <!-- This is where your job applications should be displayed -->
            <?php if ($applications_result->num_rows > 0): ?>
                <?php while ($application = $applications_result->fetch_assoc()): ?>
                    <div class="job-application">
                        <h2><?php echo htmlspecialchars($application['Job_Name']); ?></h2>
                        <p>Status: <?php echo htmlspecialchars($application['Status']); ?></p>
                        <p>Application Date: <?php echo htmlspecialchars($application['Application_Date']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>You have no job applications.</p>
            <?php endif; ?>
        </div>

        <script>
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');

            if (status === 'already_saved') {
                alert('Job already saved.');
            } else if (status === 'success') {
                alert('Job successfully saved!');
            }
        };

        function showTab(tabName) {
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => {
                tab.style.display = 'none'; // Hide all tabs
            });
            document.getElementById(tabName).style.display = 'block'; // Show the selected tab
        }

            // Send the application ID
            xhr.send("application_id=" + applicationId);
        }


        
        
        </script>
        
        
        
    </div>
</body>
</html>
