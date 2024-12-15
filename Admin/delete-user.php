<?php
// Start session
session_start();

// Check if admin is logged in
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

include '../includes/db_connect.php';

if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    
    // Step 1: Delete the user's reviews
    $deleteReviewsQuery = "DELETE FROM reviews WHERE user_id = ?";
    if ($stmtDeleteReviews = $conn->prepare($deleteReviewsQuery)) {
        $stmtDeleteReviews->bind_param("i", $user_id);
        $stmtDeleteReviews->execute();
        $stmtDeleteReviews->close();
    }

    // Step 2: Delete the user from the users table
    $deleteUserQuery = "DELETE FROM users WHERE user_id = ?";
    if ($stmtDeleteUser = $conn->prepare($deleteUserQuery)) {
        $stmtDeleteUser->bind_param("i", $user_id);
        if ($stmtDeleteUser->execute()) {
            // Redirect with success message
            header("Location: users.php?message=User and associated reviews deleted successfully");
            exit();
        } else {
            // Handle deletion error
            header("Location: users.php?error=Failed to delete user. Please try again.");
            exit();
        }
        $stmtDeleteUser->close();
    }
}

// Close connection
$conn->close();
?>
