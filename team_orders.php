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

// Check for success message in session
$successMessage = '';
if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear the message after displaying
}

// Fetch booking details related to this event team
$stmt = $conn->prepare("
    SELECT 
        b.booking_id, 
        b.user_id, 
        b.event_date, 
        b.people_attending, 
        b.status, 
        b.total_cost, 
        u.username, 
        u.email, 
        u.phone
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    WHERE b.team_id = ? AND b.status != 'Cancelled'
    ORDER BY b.booking_date DESC
");
$stmt->bind_param("i", $teamId);
$stmt->execute();
$bookings = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Orders</title>
    <link rel="stylesheet" href="css/bootstrap.css">
</head>
<body>
    <div class="container">
        <h2>Booking Orders</h2>

        <!-- Display success message -->
        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <!-- Display the bookings in a table format -->
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>User</th>
                    <th>Event Date</th>
                    <th>Number of People</th>
                    <th>Status</th>
                    <th>Total Cost</th>
                    <th>Contact</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $bookings->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['booking_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo date('d M Y', strtotime($row['event_date'])); ?></td>
                        <td>
                            <?php echo ($row['people_attending'] !== null) ? $row['people_attending'] : 'N/A'; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo number_format($row['total_cost'], 2); ?></td>
                        <td>
                            <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a><br>
                            <?php echo htmlspecialchars($row['phone']); ?>
                        </td>
                        <td>
                            <a href="chat-team.php?booking_id=<?php echo $row['booking_id']; ?>&team_id=<?php echo $teamId; ?>" class="btn btn-primary">Chat</a>
                            <a href="update_cost.php?booking_id=<?php echo $row['booking_id']; ?>" class="btn btn-warning">Update</a>
                            <a href="cancel_booking.php?booking_id=<?php echo $row['booking_id']; ?>" class="btn btn-danger">Cancel</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <br><br>
         <!-- Back to Order Button -->
         <div class="back-to-order">
           <center> <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a></center>
        </div>
    <script src="js/bootstrap.js"></script>
</body>
</html>
