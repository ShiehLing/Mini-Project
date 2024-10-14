<?php
session_start(); 
// Check if employer is logged in
if (!isset($_SESSION['employer_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$host = 'localhost';
$db = 'job_portal';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$employer_id = $_SESSION['employer_id'];

// Fetch categories for dropdown
$category_result = $conn->query("SELECT C_ID, C_Name FROM category");

// Check if the job is being edited by fetching job details based on L_ID
$job_id = $_GET['L_ID'] ?? null;
$job_details = null;

if ($job_id) {
    // Prepare a statement to fetch job details for the given job ID
    $stmt = $conn->prepare("SELECT * FROM job_listing WHERE L_ID = ? AND E_ID = ?");
    $stmt->bind_param("ii", $job_id, $employer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Fetch job details
        $job_details = $result->fetch_assoc();
    } else {
        echo '<script>
    alert("No job found or you don\'t have permission to edit this job.");
    window.location.href = "postJob.php";  
</script>';

        exit();
    }
}

$stmt = null; // Initialize $stmt to null

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_name = $_POST['job_name'];
    $category_id = $_POST['Category_ID'];
    $job_type = $_POST['job_type'];
    $salary_min = $_POST['salary_min'];
    $salary_max = $_POST['salary_max'];
    $salary = $salary_min . ' - ' . $salary_max;
    $job_responsibilities = $_POST['job_responsibilities'];
    $requirement = $_POST['requirement'];
    $add_on = $_POST['add_on'];

    if ($job_id) {
        // Update job details
        $stmt = $conn->prepare("UPDATE job_listing SET C_ID = ?, Job_Name = ?, Job_Type = ?, Salary = ?, Job_Responsibilities = ?, Requirement = ?, Add_On = ? WHERE L_ID = ? AND E_ID = ?");
        $stmt->bind_param("issssssis", $category_id, $job_name, $job_type, $salary, $job_responsibilities, $requirement, $add_on, $job_id, $employer_id);
        
        if ($stmt->execute()) {
            echo '<script>
    alert("Job updated successfully.");
    window.location.href = "JobsPosted.php";  
</script>';

        } else {
            echo '<script>
    alert("Error updating job: ' . $stmt->error . '");
    window.location.href = "postJob.php";  
</script>';

        }
    } else {
        // Insert new job listing
        $stmt = $conn->prepare("INSERT INTO job_listing (E_ID, C_ID, Job_Name, Job_Type, Salary, Job_Responsibilities, Requirement, Add_On, Created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iissssss", $employer_id, $category_id, $job_name, $job_type, $salary, $job_responsibilities, $requirement, $add_on);
        
        if ($stmt->execute()) {
           echo '<script>
    alert("New job listing created successfully.");
    window.location.href = "JobsPosted.php";  
</script>';

        } else {
            echo "Error: " . $stmt->error;
        }
    }

    if ($stmt) {
        $stmt->close(); // Close the statement if it was initialized
    }
}



$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talent Trove - Unlock Your Potential</title>
    <link rel="stylesheet" href="postJob.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="view_applications.js"></script>
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
        <a href="JobsPosted.php">Posted Jobs</a>
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
            <h1>Post A Job</h1>
            <p>Post a job and be detailed in every aspect.</p>
            <hr>

            <form action="postJob.php<?php echo $job_id ? '?L_ID=' . $job_id : ''; ?>" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="job_name"><b>Name</b></label>
        <input type="text" placeholder="Name" name="job_name" id="job_name" value="<?php echo $job_details['Job_Name'] ?? ''; ?>" required>
    </div>
    
    <div class="form-group">
        <label for="Category_ID"><b>Job Category:</b></label>
        <select id="Category_ID" name="Category_ID" required>
            <?php
            while ($category_row = $category_result->fetch_assoc()) {
                $selected = ($job_details && $job_details['C_ID'] == $category_row['C_ID']) ? 'selected' : '';
                echo "<option value='" . $category_row['C_ID'] . "' $selected>" . $category_row['C_Name'] . "</option>";
            }
            ?>
        </select>
    </div>

                <div class="form-group">
        <label for="job_type"><b>Job Type</b></label>
        <select id="job_type" name="job_type" required>
            <option value="full_time" <?php echo ($job_details && $job_details['Job_Type'] == 'full_time') ? 'selected' : ''; ?>>Full Time</option>
            <option value="part_time" <?php echo ($job_details && $job_details['Job_Type'] == 'part_time') ? 'selected' : ''; ?>>Part Time</option>
        </select>
    </div>

                <div class="form-group">
        <label for="salary_min"><b>Salary</b></label>
        <?php
        // Split salary range into min and max for pre-filling
        $salary = $job_details['Salary'] ?? '';
        list($salary_min, $salary_max) = explode(' - ', $salary . ' - ');
        ?>
        <input type="text" name="salary_min" placeholder="Min" value="<?php echo $salary_min; ?>" required>
        <input type="text" name="salary_max" placeholder="Max" value="<?php echo $salary_max; ?>" required>
    </div>

                <div class="form-group">
        <label for="job_responsibilities"><b>Job Responsibilities:</b></label>
        <textarea name="job_responsibilities" id="job_responsibilities" required><?php echo $job_details['Job_Responsibilities'] ?? ''; ?></textarea>
    </div>

                <div class="form-group">
        <label for="requirement"><b>Requirement:</b></label>
        <textarea name="requirement" id="requirement" required><?php echo $job_details['Requirement'] ?? ''; ?></textarea>
    </div>
                <div class="form-group">
                    <label for="add_on"><b>Additional Details:</b></label>
                    <textarea name="add_on" id="add_on"><?php echo $job_details['Add_On'] ?? ''; ?></textarea>

                </div>

                
                <button type="submit" name="postjobbtn" class="postjobbtn">Post Job</button>
            </form>
        </div>
    </main>


    <script>
        function openNav() {
            document.getElementById("mySidenav").style.width = "250px";
        }

        function closeNav() {
            document.getElementById("mySidenav").style.width = "0";
        }
    </script>
</body>
</html>