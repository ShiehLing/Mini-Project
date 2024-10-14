
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

// Function to handle login
function handleLogin($email, $password) {
    $conn = getDatabaseConnection();

    // Fetch user data from the database
    $stmt = $conn->prepare("SELECT U_ID, Password, Role, U_name FROM users WHERE U_Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $hashed_password, $role, $name);
    $stmt->fetch();
    
    // Verify password
    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $role;
        $_SESSION['name'] = $name; // Store name in session

        // Debugging output for user data
        echo "Logged in as User ID: $user_id, Role: $role";

        // Redirect based on role
        if ($role == 'job_seeker') {
            $_SESSION['job_seeker_id'] = $user_id;
            header("Location: index.php"); // Redirect to job seeker's dashboard
            exit();
        } elseif ($role == 'employer') {
            // Fetch employer data based on the U_ID (which is E_ID)
            $stmt = $conn->prepare("SELECT E_ID FROM employer WHERE E_ID = ?");
            $stmt->bind_param("i", $user_id); // Using U_ID as E_ID
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // Employer exists, fetch the E_ID
                $stmt->bind_result($employer_id);
                $stmt->fetch();
                $_SESSION['employer_id'] = $employer_id; // Store employer ID in session

                // Debugging output
                echo "Employer ID: " . $_SESSION['employer_id'];

                header("Location: EmployerActivity.php");
                exit();
            } else {
                echo "Error: Employer record not found.";
            }
        } else {
            echo "Invalid role.";
        }
    } else {
        echo '<script>
            alert("Invalid credentials. Please try again.");
            window.location.href = "index.php";  // Redirect to index.php after alert
          </script>';
    }

    $stmt->close();
    $conn->close();
}

// Registration function
function handleRegistration($name, $email, $password, $confirm_password, $user_type) {
    if ($password !== $confirm_password) {
        echo '<script>
                alert("Passwords do not match. Please try again.");
                window.history.back();  // Redirects to the previous page
              </script>';
        return;
    }

    $conn = getDatabaseConnection();

    // Check if the email is already in use
    $stmt = $conn->prepare("SELECT U_ID FROM users WHERE U_Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo '<script>
                alert("Email already in use. Please choose a different email.");
                window.history.back();  // Redirects to the previous page
              </script>';
        $stmt->close();
        $conn->close();
        return;
    }

    $stmt->close();

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into users table
    $stmt = $conn->prepare("INSERT INTO users (U_name, U_Email, Password, Role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $user_type);

    if ($stmt->execute()) {
        // Get the last inserted ID (user ID)
        $user_id = $conn->insert_id;

        // Insert into job_seeker or employer table based on user type
        if ($user_type === 'job_seeker') {
            $stmt = $conn->prepare("INSERT INTO job_seeker (S_ID, S_Name, S_Email) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $name, $email);
        } elseif ($user_type === 'employer') {
            $stmt = $conn->prepare("INSERT INTO employer (E_ID, E_Name, E_Email) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $name, $email);
        }

        if ($stmt->execute()) {
            // After successful registration, log the user in automatically
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = $user_type;
            $_SESSION['name'] = $name;

            // Redirect to the appropriate page based on role
            if ($user_type === 'job_seeker') {
                echo '<script>
                        alert("Registration successful! Redirecting to job seeker dashboard.");
                        window.location.href = "index.php";  // Redirect to index.php for job seeker
                      </script>';
            } elseif ($user_type === 'employer') {
                echo '<script>
                        alert("Registration successful! Redirecting to employer dashboard.");
                        window.location.href = "EmployerActivity.php";  // Redirect to EmployerActivity.php for employer
                      </script>';
            }
        } else {
            echo '<script>
                    alert("Error during registration in job_seeker/employer table: ' . $stmt->error . '");
                    window.history.back();  // Redirects to the previous page
                  </script>';
        }
    } else {
         echo '<script>
                alert("Error during registration in users table: ' . $stmt->error . '");
                window.history.back();  // Redirects to the previous page
              </script>';
    }

    $stmt->close();
    $conn->close();
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        handleLogin($email, $password);
    } elseif (isset($_POST['register'])) {
        if (isset($_POST['name'], $_POST['email'], $_POST['password'], $_POST['confirm_password'], $_POST['user_type'])) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $user_type = $_POST['user_type'];
            handleRegistration($name, $email, $password, $confirm_password, $user_type);
        } else {
            echo "Please fill out all fields!";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Double Slider Sign in/up Form</title>
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css'>
  <link rel="stylesheet" href="login.css">
</head>
<body>

<div class="container" id="container">
    <!-- Registration Form -->
    <div class="form-container sign-up-container">
        <form action="login.php" method="post">
            <h1>Create Account</h1>
            <h2>As Job Seeker Or Employer?</h2>
            <select name="user_type" id="user_type" required>
                <option value="job_seeker">Job Seeker</option>
                <option value="employer">Employer</option>
            </select>
            <br>
            <input type="text" name="name" placeholder="Name" required />
            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="password" placeholder="Password" required
   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&#+])[A-Za-z\d@$!%*?&#+]{8,}"
   title="Must contain at least one number, one uppercase, one lowercase letter, one special character (including # or +), and at least 8 or more characters" />
   
            <!-- New Confirm Password Field -->
        <input type="password" name="confirm_password" placeholder="Confirm Password" required
               pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&#+])[A-Za-z\d@$!%*?&#+]{8,}"
               title="Must match the password format above." />
        

            <input type="hidden" name="register" value="1">
            <button class="signupBtn">
                SIGN UP
            </button>
        </form>
    </div>

    <!-- Login Form -->
    <div class="form-container sign-in-container">
        <form action="login.php" method="post">
            <h1>Sign in</h1>
            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="password" placeholder="Password" required />
            <input type="hidden" name="login" value="1">
            <button class="signInBtn">
                SIGN IN
            </button>
        </form>
    </div>

    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h1>Welcome Back!</h1>
                <p>To keep connected with us please login with your personal info</p>
                <button class="ghost" id="signIn">Sign In</button>
            </div>
            <div class="overlay-panel overlay-right">
                <h1>Hello, Friend!</h1>
                <p>Enter your personal details and start journey with us</p>
                <button class="ghost" id="signUp">Sign Up</button>
            </div>
        </div>
    </div>
</div>

<script src="login.js"></script>



</body>
</html>

