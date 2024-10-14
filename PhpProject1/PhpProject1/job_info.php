<?php
// Start session
session_start();

// Database connection function
function getDatabaseConnection() {
    $conn = new mysqli('localhost', 'root', '', 'job_portal');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

$conn = getDatabaseConnection(); // Establish database connection

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);

//check if job listing ID is set 
if (!isset($_GET['job_id'])) {
    die("Job ID not specified.");
}

//get job listing ID
$job_id = intval($_GET['job_id']);

// Increment view count
    $query = "UPDATE job_listing SET view_count = IFNULL(view_count, 0) + 1 WHERE L_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $job_id);
    $stmt->execute();

// Build the base query
$query = "SELECT employer.E_Name, employer.E_ID, employer.Location, 
                 job_listing.Job_Name, job_listing.Job_Type, job_listing.Salary, 
                 job_listing.Job_Responsibilities, job_listing.Requirement, job_listing.Add_On, 
                 job_listing.Created_at, category.C_Name 
          FROM job_listing 
          JOIN employer ON job_listing.E_ID = employer.E_ID 
          JOIN category ON job_listing.C_ID = category.C_ID
          WHERE job_listing.L_ID = ?";

// Execute the query
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $job = $result->fetch_assoc();
} else {
    echo "Job not found.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($job['Job_Name']); ?></title>
    <link rel="stylesheet" href="job_info.css">
    <script src="homepage.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
    
    <div class="job-info-container">
        
        <div class="employer-info">
                    
            <div class="employer-header">
                <h2 class="employer-name"><?php echo $job['E_Name']; ?></h2>
                <a href="employer.php?e_id=<?php echo $job['E_ID']; ?>" class="view-employer">Go to this company</a>
            </div>
                    
            <p class="employer-location"></p><?php echo $job['Location']; ?></p>
                    
        </div>
        
            <?php
            // Create DateTime objects for Created_at and current time
            $createdAt = new DateTime($job['Created_at']);
            $now = new DateTime();

            // Calculate the difference
            $interval = $now->diff($createdAt);

            // Format the difference
            $timeDiff = '';
            if ($interval->y > 0) {
                $timeDiff .= $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ';
            }
            if ($interval->m > 0) {
                $timeDiff .= $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ';
            }
            if ($interval->d > 0) {
                $timeDiff .= $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ';
            }
            if ($interval->h > 0) {
                $timeDiff .= $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ';
            }
            if ($interval->i > 0) {
                $timeDiff .= $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ';
            }
            if ($interval->s > 0) {
                $timeDiff .= $interval->s . ' second' . ($interval->s > 1 ? 's' : '') . ' ';
            }

            // Fallback if the job was created very recently
            $timeDiff = trim($timeDiff) ?: 'just now';
            ?>

            <div class="job-details">
    <h1><?php echo $job['Job_Name']; ?></h1>
    <p class="text-muted" style="font-size: small;">Posted: <?php echo $timeDiff; ?> ago</p>
    <p><strong>Category:</strong> <?php echo $job['C_Name']; ?></p> <!-- New line to show category -->
    <p><strong>Type:</strong> <?php echo $job['Job_Type']; ?></p>
    <p><strong>Salary:</strong> <?php echo $job['Salary']; ?></p>
    <p><strong>Responsibilities:</strong></p>
    <p><?php echo nl2br($job['Job_Responsibilities']); ?></p>
    <p><strong>Requirements:</strong></p>
    <p><?php echo nl2br($job['Requirement']); ?></p>
    <p><strong>Add-On:</strong></p>
    <p><?php echo nl2br($job['Add_On']); ?></p>
</div>


            <div class="job-actions">
                <?php if ($is_logged_in): ?>
                    <!-- Form for Save Job -->
                    <form action="save_job.php" method="POST" onsubmit="return confirm('Do you want to save this job?');">
                        <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
                        <button type="submit" id="saveJobButton" class="btn btn-save-job">Save Job</button>
                    </form>

                    <!-- Form for Quick Apply -->
                    <form action="apply.php" method="POST" onsubmit="return confirm('Do you want to apply for this job?');">
                    <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
                    <button type="submit" id="apply-btn" class="btn btn-primary">Quick Apply</button>
                    </form>
                    
                <?php else: ?>
                    <p>Please <a href="login.php">login</a> to apply or save jobs.</p>
                <?php endif; ?>

            </div>
       
        </div>
    </div>

</body>
</html>
