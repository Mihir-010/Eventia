<?php
// Start session
session_start();

// Include database connection
require_once 'includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['userid']; // User ID from session
$teamId = isset($_GET['team_id']) ? intval($_GET['team_id']) : 0; // Team ID from GET parameter
$bookingId = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0; // Booking ID from GET parameter

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
            VALUES (?, ?, ?, ?, 'User')
        ");
        $stmt->bind_param("iiis", $bookingId, $userId, $teamId, $message);
        $stmt->execute();
    }
    
    // Redirect to avoid form resubmission
    header("Location: chat.php?booking_id=$bookingId&team_id=$teamId");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with Event Team</title>
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
            text-align: right;
        }
        .team-message {
            text-align: left;
        }
        .message-input {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Chat with Event Team</h2>
        
        <div class="chat-box">
            <?php while ($row = $messages->fetch_assoc()): ?>
                <div class="chat-message <?php echo ($row['sender'] == 'User') ? 'user-message' : 'team-message'; ?>">
                    <strong><?php echo $row['sender']; ?>:</strong> 
                    <p><?php echo htmlspecialchars($row['message']); ?></p>
                    <small><?php echo date('d M Y H:i', strtotime($row['created_at'])); ?></small>
                </div>
            <?php endwhile; ?>
        </div>
        
        <!-- Message Input Form -->
        <form method="POST" action="chat.php?booking_id=<?php echo $bookingId; ?>&team_id=<?php echo $teamId; ?>" class="message-input">
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
           <center> <a href="user_orders.php" class="btn btn-secondary">Back to Order</a></center>
        </div>

    <script src="js/bootstrap.js"></script>
</body>
</html>
