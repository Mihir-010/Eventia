<?php
// Include your database connection
include 'includes/db_connect.php';

// Start session if you're using session variables
session_start();

// Check if team_id is set (you might be passing it via GET or POST)
if (isset($_GET['team_id'])) {
    $team_id = $_GET['team_id'];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT booking_id, user_id, event_id, booking_date, event_date, people_attending, payment_status, status, total_cost FROM bookings WHERE team_id = ?");
    $stmt->bind_param("i", $team_id); // Assuming team_id is an integer

    // Execute the statement
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $bookings = [];

        // Fetch all bookings into an array
        while ($row = $result->fetch_assoc()) {
            // Define color based on booking status
            $backgroundColor = '';
            $borderColor = '';
            $textColor = '';

            switch ($row['status']) {
                case 'Confirmed':
                    $backgroundColor = 'red'; // Red background for confirmed bookings
                    $borderColor = 'red'; // Red border
                    $textColor = 'white'; // White text for contrast
                    break;
                case 'Pending':
                    $backgroundColor = 'yellow'; // Yellow background for pending bookings
                    $borderColor = 'yellow'; // Yellow border
                    $textColor = 'black'; // Black text for contrast
                    break;
                case 'Cancelled':
                    $backgroundColor = 'black'; // Black background for cancelled bookings
                    $borderColor = 'black'; // Black border
                    $textColor = 'white'; // White text for contrast
                    break;
                default:
                    $backgroundColor = 'green'; // Green for other statuses
                    $borderColor = 'green'; // Green border
                    $textColor = 'white'; // White text
                    break;
            }

            // Add color information to the booking data
            $bookings[] = [
                'id' => $row['booking_id'],
                'title' => $row['status'], // Status as the event title
                'start' => $row['event_date'], // Event start date
                'backgroundColor' => $backgroundColor, // Full background color
                'borderColor' => $borderColor, // Border color
                'textColor' => $textColor // Text color for the event
            ];
        }

        // Output bookings as JSON
        header('Content-Type: application/json');
        echo json_encode($bookings);
    } else {
        // Handle query execution failure
        echo json_encode(["error" => "Failed to fetch bookings."]);
    }

    // Close the statement
    $stmt->close();
} else {
    // Handle the case where team_id is not provided
    echo json_encode(["error" => "team_id not provided."]);
}

// Close the database connection
$conn->close();
?>
