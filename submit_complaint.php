<?php
// Include database connection
include 'includes/db_connect.php';

session_start();
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit;
}

// Initialize variables
$user_id = $_SESSION['userid'];
$team_id = $_POST['team_id'];
$complaint_text = $_POST['complaint_text'];

// Validate inputs
if (empty($team_id) || empty($complaint_text)) {
    echo "All fields are required.";
    exit;
}

// Prepare and execute SQL statement
$sql = "INSERT INTO complaints (user_id, team_id, complaint_text) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $user_id, $team_id, $complaint_text);

if ($stmt->execute()) {
    echo "Complaint submitted successfully.";
    header("refresh:2;url=user_dashboard.php"); // Redirect to the dashboard after 2 seconds
} else {
    echo "Error submitting complaint. Please try again later.";
}

$stmt->close();
$conn->close();
?>
