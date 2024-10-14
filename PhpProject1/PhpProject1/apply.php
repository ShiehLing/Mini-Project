<?php 
// Start session
session_start();

// Database connection variables
$host = 'localhost'; // your host
$user = 'root';      // your database username
$password = '';      // your database password
$database = 'job_portal'; // your database name

// Create a connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to apply for jobs.']);
    exit;
}

$user_id = intval($_SESSION['user_id']);
$job_id = intval($_POST['job_id']);

// Fetch job seeker ID using email
$query = "SELECT S_ID FROM job_seeker WHERE S_Email = (SELECT U_Email FROM users WHERE U_ID = ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'No valid job seeker account found.']);
    exit;
}

$seeker = $result->fetch_assoc();
$seeker_id = $seeker['S_ID'];

// Check if the job seeker has a resume
$query = "SELECT R_ID FROM resume WHERE S_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $seeker_id);
$stmt->execute();
$resume_result = $stmt->get_result();

if ($resume_result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'No resume found for this job seeker.']);
    exit;
}

// Fetch the resume ID (assuming there's only one resume per job seeker)
$resume = $resume_result->fetch_assoc();
$resume_id = $resume['R_ID'];

// Retrieve the employer ID (E_ID) from the job listing
$query = "SELECT E_ID FROM job_listing WHERE L_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $job_id);
$stmt->execute();
$employer_result = $stmt->get_result();

if ($employer_result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid job or employer.']);
    exit;
}

$employer = $employer_result->fetch_assoc();
$employer_id = $employer['E_ID'];


// Insert job application with default status 'pending'
$query = "INSERT INTO job_application (S_ID, L_ID, R_ID, E_ID, Application_Date, Status) 
          VALUES (?, ?, ?, ?, CURRENT_DATE, 'pending')";
$stmt = $conn->prepare($query);
$stmt->bind_param('iiii', $seeker_id, $job_id, $resume_id, $employer_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo
    '<script>
    alert("Application submitted successfully.");
    window.location.href = "MyActivityJobSeeker.php";  // Redirect to the job seeker activity page after alert
    </script>';
    
} else {
    echo 
    '<script>
    alert("Failed to submit application.");
    window.history.back();  // Optionally redirect back to the previous page
    </script>';
}

$stmt->close();
$conn->close();
?>


