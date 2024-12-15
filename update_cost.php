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

$teamId = $_SESSION['teamid']; // Team ID from session

// Get the booking ID from the URL
if (!isset($_GET['booking_id'])) {
    header("Location: team_orders.php");
    exit();
}

$bookingId = intval($_GET['booking_id']);

// Fetch booking details
$stmt = $conn->prepare("
    SELECT b.total_cost, u.username, u.email, u.phone, b.status 
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    WHERE b.booking_id = ? AND b.team_id = ?
");
$stmt->bind_param("ii", $bookingId, $teamId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Booking not found or you are not authorized to update this booking.";
    exit();
}

$booking = $result->fetch_assoc();

// Handle form submission for updating cost and status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newCost = isset($_POST['total_cost']) ? floatval($_POST['total_cost']) : 0;
    $newStatus = isset($_POST['status']) ? $_POST['status'] : '';

    // Ensure the new status is one of the ENUM values
    if ($newCost >= 0 && in_array($newStatus, ['Pending', 'Confirmed', 'Completed','Cancel'])) {
        // Update the total cost and status in the bookings table
        $stmt = $conn->prepare("UPDATE bookings SET total_cost = ?, status = ? WHERE booking_id = ?");
        $stmt->bind_param("ssi", $newCost, $newStatus, $bookingId);
        $stmt->execute();

        // Set a success message in the session
        $_SESSION['success_message'] = "The cost and status have been updated.";

        // Redirect to team_orders.php after update
        header("Location: team_orders.php");
        exit();
    } else {
        $error = "Please enter a valid cost and status.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Booking Cost</title>
    <link rel="stylesheet" href="css/bootstrap.css">
</head>
<body>
    <div class="container">
        <h2>Update Cost for Booking ID: <?php echo $bookingId; ?></h2>
        
        <p><strong>User:</strong> <?php echo htmlspecialchars($booking['username']); ?></p>
        <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($booking['email']); ?>"><?php echo htmlspecialchars($booking['email']); ?></a></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($booking['phone']); ?></p>
        <p><strong>Current Cost:</strong> ₹<?php echo number_format($booking['total_cost'], 2); ?></p>
        <p><strong>Current Status:</strong> <?php echo htmlspecialchars($booking['status']); ?></p>

        <!-- Form to update total cost and status -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="total_cost">New Total Cost</label>
                <input type="number" step="0.01" min="0" class="form-control" id="total_cost" name="total_cost" required>
            </div>
            <div class="form-group">
                <label for="status">New Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="Pending">Pending</option>
                    <option value="Confirmed">Confirmed</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancel">Cancel</option>
                </select>
            </div>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <button type="submit" class="btn btn-success">Update Cost and Status</button>
            <a href="team_orders.php" class="btn btn-secondary">Back to Orders</a>
        </form>
    </div>

    <script src="js/bootstrap.js"></script>
</body>
</html>
