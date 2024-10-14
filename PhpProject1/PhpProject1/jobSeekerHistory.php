<?php
session_start();

// Database connection
$host = 'localhost';
$db = 'job_portal';
$user = 'root'; 
$pass = ''; 

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}
$user_id = $_SESSION['user_id'];

// Set search filters (default empty)
$job_name_filter = isset($_GET['job_name']) ? $_GET['job_name'] : '';
$location_filter = isset($_GET['location']) ? $_GET['location'] : '';
$job_type_filter = isset($_GET['job_type']) ? $_GET['job_type'] : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

// Fetch categories for the dropdown
$category_query = "SELECT C_ID, C_Name FROM category";
$category_result = $conn->query($category_query);

// Fetch accepted applications with filters
$accepted_query = "
    SELECT job_application.A_ID, job_listing.Job_Name, job_listing.L_ID, job_application.Application_Date 
    FROM job_application 
    JOIN job_listing ON job_application.L_ID = job_listing.L_ID 
    JOIN employer ON job_listing.E_ID = employer.E_ID 
    WHERE job_application.S_ID = ? AND job_application.Status = 'accepted'";

if (!empty($job_name_filter)) {
    $accepted_query .= " AND job_listing.Job_Name LIKE ?";
}
if (!empty($location_filter)) {
    $accepted_query .= " AND employer.Location LIKE ?";
}
if (!empty($job_type_filter)) {
    $accepted_query .= " AND job_listing.Job_Type = ?";
}
if (!empty($category_filter)) {
    $accepted_query .= " AND job_listing.C_ID = ?";
}

$accepted_stmt = $conn->prepare($accepted_query);
if ($accepted_stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$params = [$user_id];

if (!empty($job_name_filter)) {
    $params[] = '%' . $job_name_filter . '%';
}
if (!empty($location_filter)) {
    $params[] = '%' . $location_filter . '%';
}
if (!empty($job_type_filter)) {
    $params[] = $job_type_filter;
}
if (!empty($category_filter)) {
    $params[] = $category_filter;
}

$types = 'i';
if (!empty($job_name_filter)) {
    $types .= 's';
}
if (!empty($location_filter)) {
    $types .= 's';
}
if (!empty($job_type_filter)) {
    $types .= 's';
}
if (!empty($category_filter)) {
    $types .= 'i';
}

$accepted_stmt->bind_param($types, ...$params);
$accepted_stmt->execute();
$accepted_result = $accepted_stmt->get_result();

// Fetch rejected applications with similar filters
$rejected_query = "
    SELECT job_application.A_ID, job_listing.Job_Name, job_listing.L_ID, job_application.Application_Date 
    FROM job_application 
    JOIN job_listing ON job_application.L_ID = job_listing.L_ID 
    JOIN employer ON job_listing.E_ID = employer.E_ID 
    WHERE job_application.S_ID = ? AND job_application.Status = 'rejected'";

if (!empty($job_name_filter)) {
    $rejected_query .= " AND job_listing.Job_Name LIKE ?";
}
if (!empty($location_filter)) {
    $rejected_query .= " AND employer.Location LIKE ?";
}
if (!empty($job_type_filter)) {
    $rejected_query .= " AND job_listing.Job_Type = ?";
}
if (!empty($category_filter)) {
    $rejected_query .= " AND job_listing.C_ID = ?";
}

$rejected_stmt = $conn->prepare($rejected_query);
if ($rejected_stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$params = [$user_id];

if (!empty($job_name_filter)) {
    $params[] = '%' . $job_name_filter . '%';
}
if (!empty($location_filter)) {
    $params[] = '%' . $location_filter . '%';
}
if (!empty($job_type_filter)) {
    $params[] = $job_type_filter;
}
if (!empty($category_filter)) {
    $params[] = $category_filter;
}

$types = 'i';
if (!empty($job_name_filter)) {
    $types .= 's';
}
if (!empty($location_filter)) {
    $types .= 's';
}
if (!empty($job_type_filter)) {
    $types .= 's';
}
if (!empty($category_filter)) {
    $types .= 'i';
}

$rejected_stmt->bind_param($types, ...$params);
$rejected_stmt->execute();
$rejected_result = $rejected_stmt->get_result();

$accepted_stmt->close();
$rejected_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Seeker Activity - Application History</title>
    <link rel="stylesheet" href="jobSeekerHistory.css?v=1">
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

    
    
            <!-- Search Form -->
        
           <div id="main" class="activity-section">
                <div id="search-container" class="search-container">
                    <form method="GET" action="" class="search-form">
                        <label for="job_name" class="form-label">Job Name:</label>
                                <input type="text" name="job_name" class="form-control" value="<?= htmlspecialchars($job_name_filter) ?>">

                        <label for="location" class="form-label">Location:</label>
                                <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($location_filter) ?>">

                         <label for="job_type" class="form-label">Job Type:</label>
                                <select name="job_type" class="form-select">
                                    <option value="">All</option>
                                    <option value="full_time" <?= $job_type_filter == 'full_time' ? 'selected' : '' ?>>Full Time</option>
                                    <option value="part_time" <?= $job_type_filter == 'part_time' ? 'selected' : '' ?>>Part Time</option>
                                </select>

                        <label for="category" class="form-label">Category:</label>
                                <select name="category" class="form-select">
                                    <option value="">All</option>
                                    <?php while ($row = $category_result->fetch_assoc()): ?>
                                        <option value="<?= $row['C_ID'] ?>" <?= $category_filter == $row['C_ID'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($row['C_Name']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>


                        <button type="submit" class="search-btn">Search</button>
                    </form>
                </div>
    
    
    
    <!-- Main Content -->
    <div id="main" class="activity-section">
        <div class="radio-inputs">
            <label class="radio">
                <input type="radio" name="activity" checked onclick="showTab('accepted')" />
                <span class="name">Accepted</span>
            </label>
            <label class="radio">
                <input type="radio" name="activity" checked onclick="showTab('rejected')" />
                <span class="name">Rejected</span>
            </label>
        </div>

        


        <div id="accepted" class="activity-tab">
            <?php if ($accepted_result->num_rows > 0): ?>
                <?php while ($row = $accepted_result->fetch_assoc()): ?>
                    <div class="accepted">
                        <h4><?php echo htmlspecialchars($row['Job_Name']); ?></h4>
                        <p>Date Applied: <?php echo htmlspecialchars($row['Application_Date']); ?></p>
                        <a href="job_info.php?job_id=<?php echo $row['L_ID']; ?>" class="btn btn-primary">View Job Info</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No accepted applications found.</p>
            <?php endif; ?>
        </div>

        <div id="rejected" class="activity-tab" style="display:none;">
            <?php if ($rejected_result->num_rows > 0): ?>
                <?php while ($row = $rejected_result->fetch_assoc()): ?>
                    <div class="rejected">
                        <h4><?php echo htmlspecialchars($row['Job_Name']); ?></h4>
                        <p>Date Applied: <?php echo htmlspecialchars($row['Application_Date']); ?></p>
                        <a href="job_info.php?job_id=<?php echo $row['L_ID']; ?>" class="btn btn-primary">View Job Info</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No rejected applications found.</p>
            <?php endif; ?>
        </div>
    </div>



    <script>
        function showTab(tab) {
            document.getElementById('accepted').style.display = tab === 'accepted' ? 'block' : 'none';
            document.getElementById('rejected').style.display = tab === 'rejected' ? 'block' : 'none';
        }
    </script>
</body>
</html>
