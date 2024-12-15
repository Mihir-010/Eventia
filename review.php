<?php
// Start session
session_start();

// Include database connection
require_once 'includes/db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch user ID and team ID from POST data
    $userId = $_SESSION['userid'];
    $teamId = $_POST['team_id'];
    $newRating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Check if the user has already submitted a review for this team
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM reviews WHERE user_id = ? AND team_id = ?");
    $stmt->bind_param("ii", $userId, $teamId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        header("Location: user_orders.php?error=You have already submitted a review for this team.");
        exit();
    }

    // Insert the new review
    $stmt = $conn->prepare("INSERT INTO reviews (user_id, team_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $userId, $teamId, $newRating, $comment);
    if ($stmt->execute()) {
        
        // Fetch current rating and review count for the team
        $stmt = $conn->prepare("SELECT rating, review_count FROM eventteams WHERE team_id = ?");
        $stmt->bind_param("i", $teamId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $currentRating = $row['rating'];
        $currentReviewCount = $row['review_count'];

        // Calculate new average rating
        $newAverageRating = (($currentRating * $currentReviewCount) + $newRating) / ($currentReviewCount + 1);
        $newReviewCount = $currentReviewCount + 1;

        // Update the team's rating and review count in the eventteams table
        $stmt = $conn->prepare("UPDATE eventteams SET rating = ?, review_count = ? WHERE team_id = ?");
        $stmt->bind_param("dii", $newAverageRating, $newReviewCount, $teamId);
        $stmt->execute();

        // Redirect back to user orders with a success message
        header("Location: user_orders.php?success=Review submitted successfully!");
        exit();
    } else {
        // Handle error
        header("Location: user_orders.php?error=Error submitting review. Please try again.");
        exit();
    }
}
?>
