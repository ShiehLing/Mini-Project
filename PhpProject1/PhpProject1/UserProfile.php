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

// Prepare the SQL query to fetch user data
$sql = "SELECT users.U_Email, job_seeker.S_Name, job_seeker.Gender, job_seeker.Age, job_seeker.S_PhoneNo, job_seeker.Address, job_seeker.AboutMe, resume.Detail 
FROM job_seeker
LEFT JOIN users ON job_seeker.S_Email = users.U_Email
LEFT JOIN resume ON job_seeker.S_ID = resume.S_ID
WHERE users.U_ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($email, $name, $gender, $age, $phoneNo, $Address, $aboutMe, $resumeDetails);

if (!$stmt->fetch()) {
    die("Error fetching user data.");
}

$stmt->close(); // Close the statement

// Split the name into first and last name
$nameParts = explode(' ', $name);
$firstName = $nameParts[0];
$lastName = isset($nameParts[1]) ? $nameParts[1] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $Address = $_POST['Address'];
    $aboutMe = $_POST['aboutMe'];
    $resumeDetails = $_POST['resume_Detail'];

    // Update job seeker details
    $sql = "UPDATE job_seeker 
            SET S_Name = ?, Gender = ?, Age = ?, S_PhoneNo = ?, Address = ?, AboutMe = ?
            WHERE S_ID = ?";
    $stmt = $conn->prepare($sql);
    $fullName = $firstName . ' ' . $lastName;
    $stmt->bind_param("ssisssi", $fullName, $gender, $age, $phoneNumber, $Address, $aboutMe, $user_id);

    if ($stmt->execute()) {
        // Check if resume already exists for this user
        $sql = "SELECT R_ID FROM resume WHERE S_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // If a resume exists, update the resume details
            $sql = "UPDATE resume SET Detail = ? WHERE S_ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $resumeDetails, $user_id);
        } else {
            // If no resume exists, insert a new one
            $sql = "INSERT INTO resume (S_ID, Detail) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $user_id, $resumeDetails);
        }

        if ($stmt->execute()) {
            // Set a session variable to indicate success
            $_SESSION['success'] = true;
            // Redirect to the same page
            header("Location: UserProfile.php");
            exit();
        } else {
            die("Error updating resume.");
        }
    } else {
        die("Error updating job seeker details.");
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="UserProfile.js"></script>
        <link rel="stylesheet" href="UserProfile.css"/>
        <link href="https://fonts.googleapis.com/css2?family=YourFont:wght@400;600&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <title>Job Seeker Profile</title>
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


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>




        <main class="main-content">
            <div class="container">
                <h1>Job Seeker Profile</h1>
                <p>Employers can view this profile when a job seeker applies for a job.</p>
                <hr>

                <form action="userProfile.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group mb-3">
                        <label for="firstName"><b>First Name</b></label>
                        <input type="text" placeholder="First Name" name="firstName" id="firstName" value="<?php echo $firstName; ?>" required>
                        <label for="lastName"><b>Last Name</b></label>
                        <input type="text" placeholder="Last Name" name="lastName" id="lastName" value="<?php echo $lastName; ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="age"><b>Age</b></label>
                        <input type="text" placeholder="Age" name="age" id="age" value="<?php echo $age; ?>" required>

                        <label for="gender"><b>Gender</b></label>
                        <select name="gender" id="gender" required>
                            <option value="Male" <?php echo ($gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="email"><b>Email</b></label>
                        <input type="email" placeholder="Enter Email" name="email" id="email" value="<?php echo $email; ?>" required>

                        <label for="phoneNumber"><b>Phone Number</b></label>
                        <input type="text" placeholder="Enter Phone Number" name="phoneNumber" id="phoneNumber" value="<?php echo $phoneNo; ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="address"><b>Address</b></label>
                        <textarea name="Address" id="Address" placeholder="Enter your address" rows="4"><?php echo $Address; ?></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="aboutMe"><b>About Me</b></label>
                        <textarea name="aboutMe" id="aboutMe" placeholder="Write about yourself" rows="4"><?php echo $aboutMe; ?></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="resume_Detail"><b>Resume Details</b></label>
                        <textarea name="resume_Detail" id="resume_Detail" rows="10" placeholder="Write about your Education, experiences, and skills. The more detailed the higher the chance of accpetance"><?php echo isset($resumeDetails) ? htmlspecialchars($resumeDetails) : ''; ?></textarea>
                    </div>
                        <button type="submit" class="registerbtn">Save Changes</button>
                </form>
            </div>
        </main>

        
       <!-- Modal for confirmation -->
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmModalLabel">Confirm Submission</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to submit the changes?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmSubmit">Submit</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="successModalLabel">Submission Successful</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Your changes have been submitted successfully.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.querySelector('form').addEventListener('submit', function(event) {
                event.preventDefault();
                var myModal = new bootstrap.Modal(document.getElementById('confirmModal'));
                myModal.show();
            });

            document.getElementById('confirmSubmit').addEventListener('click', function() {
                document.querySelector('form').submit();
            });

            // Check if the success flag is set in the session
            <?php if (isset($_SESSION['success']) && $_SESSION['success']): ?>
                <?php unset($_SESSION['success']); ?>
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            <?php endif; ?>
        </script>


        
        
    </body>
</html>