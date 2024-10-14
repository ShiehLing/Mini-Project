<?php
// Start session to access session variables
session_start();

$servername = "localhost";
$username = "root"; // default username for XAMPP
$password = ""; // default password for XAMPP (empty)
$dbname = "job_portal"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in user's ID from session
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
if (!$user_id) {
    die("User not logged in."); // Ensure user is logged in
}

// Fetch user's data including the profile image
$sql = "SELECT users.U_Email, employer.E_Name, employer.About_Company, employer.E_PhoneNo, employer.Location, employer.E_profile_image
        FROM users 
        INNER JOIN employer ON users.U_ID = employer.E_ID 
        WHERE users.U_ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($email, $company_Name, $about_Company, $phoneNo, $location, $profileImagePath);

if (!$stmt->fetch()) {
    die("Error fetching user data.");
}

$stmt->close();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $company_Name = $_POST['company_Name'];
    $email = $_POST['email'];
    $phoneNo = $_POST['phoneNo'];
    $about_Company = $_POST['about_Company'];
    $location = $_POST['location'];
    


    // Update data in the employer table
$sql = "UPDATE employer 
        SET E_Name = ?, E_PhoneNo = ?, About_Company = ?, Location = ?, E_profile_image = ? 
        WHERE E_ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssi", $company_Name, $phoneNo, $about_Company, $location, $profileImagePath, $user_id); // Fixed here


    if (!$stmt->execute()) {
        die("Error updating employer data: " . $stmt->error);
    }

    $stmt->close();

    // Update email in the users table
    $sql = "UPDATE users 
            SET U_Email = ? 
            WHERE U_ID = ?";

    
    // Debugging line to check if form submission reaches here
echo "Email form submitted successfully"; // Add this for debugging
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $email, $user_id);

if ($stmt->execute()) {
    echo "Record updated successfully";
    header("Location: employerProfile.php"); // Redirect to the same page to see changes
    exit();
} else {
    die("Error updating user email: " . $stmt->error);
}



    // Close connection
    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="employerProfile.js"></script>
    <link rel="stylesheet" href="employerProfile.css"/>
    <script src="homepage.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=YourFont:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <title>Employer Profile</title>
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

    
    <main class="main-content">
    <div class="container">
        <h1>Employer Profile</h1>
        <hr>

 

            <form action="employerProfile.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    
                     
                                  
                    
                    <label for="company_Name"><b>Company Name</b></label>
                    <input type="text" placeholder="companyName" name="company_Name" id="company_Name" value="<?php echo $company_Name; ?>" required>
                
                    <label for="email"><b>Email</b></label>
                    <input type="email" placeholder="Email" name="email" id="email" value="<?php echo $email; ?>" required>
                
                    <label for="phoneNo"><b>Phone Number</b></label>
                    <input type="text" placeholder="Phone Number" name="phoneNo" id="phoneNo" value="<?php echo $phoneNo; ?>" required>

                    <label for="about_Company"><b>About Company</b></label>
                    <textarea name="about_Company" id="about_Company" placeholder="About Company" row="4"><?php echo $about_Company; ?></textarea>

                    <label for="location">Location</label>
                    <input type="text" placeholder="Location" id="location" name="location" value="<?php echo $location; ?>" required>

                  

                <button type="submit" class="registerbtn">Save Changes</button>
            </form>
        
    </main>

    <script src="employerProfile.js"></script>    
    
   
</body>
</html>