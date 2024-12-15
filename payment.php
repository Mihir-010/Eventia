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

// Fetch booking ID and amount from URL
$bookingId = $_GET['booking_id'];
$amount = $_GET['amount']; // Amount is now the total cost in the original currency unit

// Initialize success and error messages
$success_message = '';
$error_message = '';

// Handle simulated payment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Simulate payment processing (update the database)
    $stmt = $conn->prepare("UPDATE bookings SET payment_status = 'Paid', status = 'Confirmed' WHERE booking_id = ?");
    $stmt->bind_param("i", $bookingId);
    
    if ($stmt->execute()) {
        // Successful payment
        $success_message = "Payment of ₹" . htmlspecialchars($amount) . " successful! Thank you for your order.";
    } else {
        // Error during payment processing
        $error_message = "Error processing payment. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <h2>Payment</h2>

    <!-- Display success or error message -->
    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
        <a href="user_orders.php" class="btn btn-primary">View My Bookings</a>
    <?php elseif ($error_message): ?>
        <div class="alert alert-danger">
            <?php echo $error_message; ?>
        </div>
    <?php else: ?>
        <!-- Payment Form -->
        <form method="POST" action="">
            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($bookingId); ?>">
            <div class="form-group">
                <label for="amount">Amount to Pay:</label>
                <input type="text" class="form-control" id="amount" value="₹<?php echo htmlspecialchars($amount); ?>" readonly>
            </div>
            <button type="submit" class="btn btn-success">Confirm Payment</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
