<?php
// Start session
session_start();

// Include database connection
require_once 'includes/db_connect.php';

// Check if the event team or user is logged in
if (!isset($_SESSION['teamid']) && !isset($_SESSION['userid'])) {
    header("Location: login_event_team.php"); // Redirect to the appropriate login page
    exit();
}

// Retrieve booking_id from the query string
if (isset($_GET['booking_id'])) {
    $bookingId = intval($_GET['booking_id']);
    
    // Update the status of the booking to "Cancelled"
    $stmt = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE booking_id = ?");
    $stmt->bind_param("i", $bookingId);

    if ($stmt->execute()) {
        // If successfully updated, redirect back to the bookings or orders page
        if (isset($_SESSION['teamid'])) {
            header("Location: team_orders.php"); // Redirect to team orders page
        } elseif (isset($_SESSION['userid'])) {
            header("Location: orders.php"); // Redirect to user orders page
        }
        exit();
    } else {
        // Handle query failure
        echo "Error cancelling booking.";
    }
} else {
    // If no booking ID is provided, redirect to the appropriate page
    if (isset($_SESSION['teamid'])) {
        header("Location: team_orders.php");
    } elseif (isset($_SESSION['userid'])) {
        header("Location: orders.php");
    }
    exit();
}
?>
