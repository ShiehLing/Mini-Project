<?php
// Start session
session_start();

// Check if user is logged in
$logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talent Trove - Unlock Your Potential</title>
    <link rel="stylesheet" href="about_us.css">
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
    
    <main class="container about-us my-5">
        <h2 class="text-center mb-4">About Talent Trove</h2>
        <div class="row">
            <div class="col-md-8 mx-auto">
                <p>Welcome to <strong>Talent Trove</strong>, a dynamic platform designed to connect talented job seekers with forward-thinking employers. Our mission is to empower individuals to unlock their full potential while providing businesses with access to top talent in various industries.</p>

                <h4>Our Mission</h4>
                <p>We aim to simplify the job search process by offering an easy-to-use interface where job seekers can showcase their skills and find the perfect role. Meanwhile, employers can discover and recruit the right candidates to help their businesses grow and succeed.</p>

                <h4>What We Offer</h4>
                <ul>
                    <li><strong>For Job Seekers:</strong> An extensive job database, personalized career recommendations, and tools to create a professional profile that stands out to employers.</li>
                    <li><strong>For Employers:</strong> Access to a diverse pool of talent, advanced candidate filtering options, and tools to manage the hiring process efficiently.</li>
                </ul>

                <h4>Why Choose Us?</h4>
                <p>At Talent Trove, we believe that finding the right job or the right candidate should be a seamless experience. Whether you're searching for your dream job or looking to build a high-performing team, Talent Trove is here to support you every step of the way.</p>

                <p class="text-center mt-4"><strong>Join us and unlock your potential with Talent Trove!</strong></p>
            </div>
        </div>
    </main>

</body>
</html>