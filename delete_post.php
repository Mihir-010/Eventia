<?php
// Start session
session_start();

// Include database connection
require_once 'includes/db_connect.php';

// Check if the event team is logged in
if (!isset($_SESSION['teamid'])) {
    header("Location: login_event_team.php");
    exit();
}

// Check if post_id is set
if (isset($_GET['post_id'])) {
    $postId = $_GET['post_id'];
    $teamId = $_SESSION['teamid'];

    // Prepare SQL to delete the post
    $stmt = $conn->prepare("DELETE FROM event_team_post WHERE post_id = ? AND team_id = ?");
    $stmt->bind_param("ii", $postId, $teamId);

    if ($stmt->execute()) {
        // Redirect to dashboard with a success message
        header("Location: dashboard.php?message=Post deleted successfully");
    } else {
        // Redirect to dashboard with an error message
        header("Location: dashboard.php?error=Failed to delete post");
    }
    exit();
} else {
    // If no post_id is set, redirect to dashboard
    header("Location: dashboard.php");
    exit();
}
?>
