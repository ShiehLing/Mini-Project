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

// Check if the user is logged in
$logged_in = isset($_SESSION['U_ID']);

// Initialize variables to store search parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$job_type = isset($_GET['job_type']) ? $_GET['job_type'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
        
// Build the base query
$query = "SELECT job_listing.*, employer.E_Name, employer.Location, category.C_Name 
          FROM job_listing 
          JOIN employer ON job_listing.E_ID = employer.E_ID 
          JOIN category ON job_listing.C_ID = category.C_ID 
          WHERE job_listing.status = 'available'";  

// Add search and filter conditions
if (!empty($search)) {
    $query .= " AND (job_listing.Job_Name LIKE '%$search%' OR employer.E_Name LIKE '%$search%')";
}

if (!empty($location)) {
    $query .= " AND employer.Location LIKE '%$location%'";
}

if (!empty($category)) {
    $query .= " AND category.C_ID = '$category'";
}

if (!empty($job_type)) {
    $query .= " AND job_listing.Job_Type LIKE '%$job_type%'";
}

// Execute the query
$result = $conn->query($query);

// Determine if there are results
$no_results = $result->num_rows == 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings</title>
    <link rel="stylesheet" href="job_listing.css">
    <script src="homepage.js"></script>
    <script src="job_listing.js"></script>
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

    <div class="job-listing-container">
        <h2>Browse Available Jobs</h2>

        <ul class="job-list">
            <?php if ($no_results): ?>
                <script>
                    alert("No job listings or company names found matching your criteria.");
                    window.location.href = "index.php";  // Redirect to index.php after alert
                </script>
            <?php else: ?>

                <?php 
                while($row = $result->fetch_assoc()): 
                    // Create DateTime objects
                    $createdAt = new DateTime($row['Created_at']);
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
                
                    <!-- Wrap the entire <li> with a link to make it clickable -->
                    <a href="job_info.php?job_id=<?php echo $row['L_ID']; ?>" class="job-link">
                        <li class="job-item">
                            <div class="job-details">
                            <h3><?php echo $row['Job_Name']; ?></h3>
                            <p><strong>Company:</strong> <?php echo $row['E_Name']; ?></p>
                            <p><strong>Location:</strong> <?php echo $row['Location']; ?></p>
                            <p><strong>Category:</strong> <?php echo $row['C_Name']; ?></p>
                            <p><strong>Job Type:</strong> <?php echo $row['Job_Type']; ?></p>
                            <p><strong>Salary:</strong> <?php echo $row['Salary']; ?></p>
                            <p style="font-size: small; color: gray;">Posted: <?php echo $timeDiff; ?> ago</p>
                            </div>
                        </li>
                    </a>
                <?php endwhile; ?>
            <?php endif; ?>
        </ul>
    </div>

</body>
</html>
