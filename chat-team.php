<?php
// Start session
session_start();

// Include database connection
require_once 'includes/db_connect.php';

// Check if team is logged in
if (!isset($_SESSION['teamid'])) {
    header("Location: login_event_team.php");
    exit();
}

$teamId = $_SESSION['teamid']; // Team ID from session
$bookingId = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0; // Booking ID from GET parameter

// Fetch user_id from bookings table for this booking
$stmt = $conn->prepare("SELECT user_id FROM bookings WHERE booking_id = ? AND team_id = ?");
$stmt->bind_param("ii", $bookingId, $teamId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Invalid booking or you do not have access to this chat.");
}
$booking = $result->fetch_assoc();
$userId = $booking['user_id'];

// Fetch messages from the chats table
$stmt = $conn->prepare("
    SELECT message, sender, created_at 
    FROM chats 
    WHERE booking_id = ? 
    ORDER BY created_at ASC
");
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$messages = $stmt->get_result();

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];

    if (!empty($message)) {
        // Insert the new message into the database
        $stmt = $conn->prepare("
            INSERT INTO chats (booking_id, user_id, team_id, message, sender) 
            VALUES (?, ?, ?, ?, 'Team')
        ");
        $stmt->bind_param("iiis", $bookingId, $userId, $teamId, $message);
        $stmt->execute();
    }

    // Redirect to avoid form resubmission
    header("Location: chat-team.php?booking_id=$bookingId");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with User</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <style>
        .chat-box {
            border: 1px solid #ddd;
            padding: 10px;
            height: 400px;
            overflow-y: scroll;
        }
        .chat-message {
            margin-bottom: 10px;
        }
        .user-message {
            text-align: left;
        }
        .team-message {
            text-align: right;
        }
        .message-input {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Chat with User</h2>
        
        <div class="chat-box">
            <?php while ($row = $messages->fetch_assoc()): ?>
                <div class="chat-message <?php echo ($row['sender'] == 'Team') ? 'team-message' : 'user-message'; ?>">
                    <strong><?php echo $row['sender']; ?>:</strong> 
                    <p><?php echo htmlspecialchars($row['message']); ?></p>
                    <small><?php echo date('d M Y H:i', strtotime($row['created_at'])); ?></small>
                </div>
            <?php endwhile; ?>
        </div>
        
        <!-- Message Input Form -->
        <form method="POST" action="chat-team.php?booking_id=<?php echo $bookingId; ?>" class="message-input">
            <div class="input-group">
                <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Send</button>
                </div>
            </div>
        </form>
    </div>
    <br><br>
     <!-- Back to Order Button -->
     <div class="back-to-order">
           <center> <a href="team_orders.php" class="btn btn-secondary">Back to Order</a></center>
        </div>
    <script src="js/bootstrap.js"></script>
</body>
</html>
