<?php
session_start();
$host = 'localhost';
$db = 'job_portal';
$user = 'root'; 
$pass = ''; 

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the application ID from the POST request
if (isset($_POST['application_id'])) {
    $application_id = intval($_POST['application_id']);

    // Fetch the application status (for simplicity, we set "accepted" or "rejected" here)
    $status = "accepted";  // This is an example. Use logic to determine status based on conditions.

    // Update the application status
    $update_query = "UPDATE job_application SET Status = ? WHERE A_ID = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('si', $status, $application_id);
    
    if ($stmt->execute()) {
        echo "Status updated successfully.";
    } else {
        echo "Error updating status.";
    }

    $stmt->close();
}

$conn->close();
?>
