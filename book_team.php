<?php
session_start();
include 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_id = $_POST['team_id'];
    $event_date = $_POST['event_date'];
    $num_people = isset($_POST['num_people']) && !empty($_POST['num_people']) ? $_POST['num_people'] : NULL;
    
    // Assuming the user is logged in and has a user ID stored in the session
    $user_id = $_SESSION['userid'];

    // Check the team's category
    $team_stmt = $conn->prepare("SELECT category FROM eventteams WHERE team_id = ?");
    $team_stmt->bind_param("i", $team_id);
    $team_stmt->execute();
    $team_result = $team_stmt->get_result();
    $team = $team_result->fetch_assoc();
    
    $team_category = $team['category'];
    $team_stmt->close();

    // Insert the booking into the database
    if ($team_category === 'Venue') {
        // Ensure num_people is not NULL for Venue bookings
        if ($num_people === NULL) {
            echo "Number of people attending is required for Venue bookings.";
            exit();
        }
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, team_id, event_date, people_attending) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $user_id, $team_id, $event_date, $num_people);
    } else {
        // For Decoration and Catering, num_people can be NULL
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, team_id, event_date, people_attending) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $user_id, $team_id, $event_date, $num_people);
    }

    if ($stmt->execute()) {
        echo "<script>
                alert('Booking successful! Redirecting to your dashboard...');
                window.location.href = 'user_orders.php';
              </script>";
        exit();
    } else {
        echo "Error booking team: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
