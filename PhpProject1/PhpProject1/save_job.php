<?php
// Start session
session_start();

function getDatabaseConnection() {
    $conn = new mysqli('localhost', 'root', '', 'job_portal');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to save jobs.']);
    exit;
}

$user_id = $_SESSION['user_id']; 
$job_id = $_POST['job_id']; 

$conn = getDatabaseConnection(); // Establish database connection

// Check if the job is already saved by the user
$query = "SELECT * FROM saved_jobs WHERE S_ID  = ? AND L_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $user_id, $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Redirect to myActivityJobSeeker.php with message "Job already saved"
    header("Location: myActivityJobSeeker.php?status=already_saved");
    exit;
}

// Insert into saved jobs table
$query = "INSERT INTO saved_jobs (S_ID, L_ID) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $user_id, $job_id);

if ($stmt->execute()) {
    // Redirect to myActivityJobSeeker.php with success message
    header("Location: myActivityJobSeeker.php?status=success");
    exit; // Ensure no further code is executed
} else {
    echo "Error saving job.";
}

$stmt->close();
$conn->close();
?>
