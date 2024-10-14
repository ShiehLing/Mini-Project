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
$logged_in = isset($_SESSION['U_ID'], $_SESSION['Role']); // Check if session variables exist

if ($logged_in) {
    $U_ID = $_SESSION['U_ID']; // Retrieve user ID from session

    // Query to fetch the user's data, including the profile_image
    $query = "SELECT U_profile_image FROM users WHERE U_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $U_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $U_profile_image = $user['U_profile_image'];

    // If profile_image is empty, set the default avatar
    if (empty($U_profile_image)) {
        $U_profile_image = 'avatar.png';
    }
}

// Check if employer ID is set
if (!isset($_GET['e_id'])) {
    die("Employer ID not specified.");
}

// Get employer ID
$e_id = intval($_GET['e_id']);

// Fetch employer details
$query = "SELECT E_profile_image, E_Name, E_Email, E_PhoneNo, Location, About_Company FROM employer WHERE E_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $e_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $employer = $result->fetch_assoc();
} else {
    echo "Employer not found.";
    exit;
}

// Fetch open positions for the employer
$jobQuery = "SELECT L_ID, Job_Name, Job_Type, Salary, Requirement FROM job_listing WHERE E_ID = ? AND status = 'available'";
$jobStmt = $conn->prepare($jobQuery);
$jobStmt->bind_param('i', $e_id);
$jobStmt->execute();
$jobResult = $jobStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($employer['E_Name']); ?> - Employer Profile</title>
    <link rel="stylesheet" href="employer.css">
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
    
    <div class="employer-name">
        <h1><?php echo htmlspecialchars($employer['E_Name']); ?></h1>
    </div>
    
    <div class="employer-details">
        <p><strong>Email:</strong> <?php echo htmlspecialchars($employer['E_Email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($employer['E_PhoneNo']); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($employer['Location']); ?></p>
        <p><strong>About Company:</strong></p>
        <p><?php echo nl2br(htmlspecialchars($employer['About_Company'])); ?></p>
    </div>

    <div class="open-positions">
        <img src="suitcase.png" class="suitcase-icon" alt="suitcase-icon"> 
        <h2>Open Positions</h2>
    </div>
    
    <section class="job-listing">
        <div class="container">
            <div class="job-listing-inner">
                <div class="row">
                    <?php while ($job = $jobResult->fetch_assoc()): ?>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="single-job">
                                <div class="inner">
                                    <div class="icon">
                                        <i class="fa fa-briefcase"></i> 
                                    </div>
                                    
                                    <div class="single-content">
                                        <h4><?php echo htmlspecialchars($job['Job_Name']); ?></h4>
                                        
                                        <li>
                                            <strong><?php echo htmlspecialchars($job['Job_Type'] ?? 'Not specified'); ?></strong> 
                                        </li>
                                        
                                        <li>
                                            <strong><?php echo htmlspecialchars($job['Salary'] ?? 'Not specified'); ?></strong> 
                                        </li>
                                        
                                        <a class="arrow-icon" href="job_info.php?job_id=<?php echo $job['L_ID']; ?>">
                                            LEARN MORE &#8594;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </section>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>


