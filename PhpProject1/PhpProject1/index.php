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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talent Trove - Unlock Your Potential</title>
    <link rel="stylesheet" href="homepage.css">
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
                        
                        <li class="nav-item">
                            <a class="dropdown-item" href="logout.php" id="logoutButton">Logout</a>
                        </li>
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
    
    <section class="hero">
        <h2>Find Your Dream Job with Talent Trove</h2>
        <div class="search-container">
            <form action="job_listing.php" method="get" class="search-form d-flex align-items-center">
                <input type="text" name="search" placeholder="Enter job title or company name" id="job-search" class="form-control me-2">
                
                // Query to fetch distinct locations from the employer table
                <?php $location_result = $conn->query("SELECT DISTINCT Location FROM employer"); 
                if ($location_result->num_rows > 0): ?>
                
                    <select name="location" id="location" class="form-select me-2">
                        <option value="">Select Location</option>
                        <!-- Populate locations from the database -->
                        <?php while ($location = $location_result->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($location['Location']); ?>">
                                <?php echo htmlspecialchars($location['Location']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                <?php else: ?>
                    <p>No locations available.</p>
                <?php endif; ?>

                <select name="job_type" id="job_type" class="form-select me-2">
                    <option value="">Select Type of Job</option>
                    <option value="Part Time">Part Time</option>
                    <option value="Full Time">Full Time</option>
                </select>
                
                <select name="category" id="category" class="form-select me-2">
                    <option value="">Select Category</option>
                    <!-- Populate categories from the database -->
                    <?php
                    $category_result = $conn->query("SELECT C_ID, C_Name FROM category");
                    while ($category = $category_result->fetch_assoc()): ?>
                        <option value="<?php echo $category['C_ID']; ?>"><?php echo $category['C_Name']; ?></option>
                    <?php endwhile; ?>
                     
                    <!-- search & get data from database -->
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
                    $search = $conn->real_escape_string($_GET['search']);
                    $location = $conn->real_escape_string($_GET['Location']);
                    $job_type = $conn->real_escape_string($_GET['Job_Type']);
                    $category = $conn->real_escape_string($_GET['category']);

                    $query = "SELECT * FROM jobs WHERE Job_Name LIKE '%$search%'";

                    if (!empty($Location)) {
                        $query .= " AND Location='$Location'";
                    }
                    if (!empty($Job_Type)) {
                        $query .= " AND Job_Type='$Job_Type'";
                    }
                    if (!empty($category)) {
                        $query .= " AND C_ID='$category'";
                    }

                    $result = $conn->query($query);
                    }?>

                    <!-- Check if form is submitted -->
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    
                    //Check if Category_ID is set and is valid
                    }
                    if (isset($_POST['C_ID']) && !empty($_POST['C_ID'])) {
                    $C_ID = $conn->real_escape_string($_POST['C_ID']);
        
                    $checkCategory = $conn->query("SELECT * FROM category WHERE C_ID = '$C_ID'");
                    if ($checkCategory->num_rows == 0) {
                        die('Invalid Category ID. Value: ' . htmlspecialchars($C_ID));
                    }
                } else {
                    die('C_ID is empty. Value: ' . htmlspecialchars($_POST['C_ID']));
                }?>
                </select>
            </form>
        </div>
        
        <div class="search-results">
            <!-- Display job search results here -->
            <?php if (isset($result) && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="job-item">
                        <h3><?php echo $row['Job_Name']; ?></h3>
                        <p><strong>Company:</strong> <?php echo $row['Company_Name']; ?></p>
                        <p><strong>Location:</strong> <?php echo $row['Location']; ?></p>
                        <p><strong>Category:</strong> <?php echo $row['C_Name']; ?></p>
                        <p><strong>Job Type:</strong> <?php echo $row['Job_Type']; ?></p>
                        <p><strong>Salary:</strong> <?php echo $row['Salary']; ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No results found.</p>
            <?php endif; ?>
        
        </div>
        
    </section>
    
     <footer class="footer">
        <div class="container text-center py-3">
            <p>&copy; 2024 Talent Trove. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
