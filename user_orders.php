<?php
// Start session
session_start();

// Include database connection
require_once 'includes/db_connect.php';
require_once 'header.php';

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Fetch user ID from session
$userId = $_SESSION['userid'];

// Handle booking cancellation
if (isset($_POST['cancel_booking'])) {
    $bookingId = $_POST['booking_id'];
    $stmt = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE booking_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $bookingId, $userId);
    if ($stmt->execute()) {
        $success_message = "Booking cancelled successfully!";
    } else {
        $error_message = "Error cancelling booking. Please try again.";
    }
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $teamId = $_POST['team_id'];
    $newRating = $_POST['rating'];

    // Fetch current rating and number of reviews
    $stmt = $conn->prepare("SELECT rating, review_count FROM eventteams WHERE team_id = ?");
    $stmt->bind_param("i", $teamId);
    $stmt->execute();
    $result = $stmt->get_result();
    $team = $result->fetch_assoc();

    if ($team) {
        $currentRating = $team['rating'];
        $reviewCount = $team['review_count'];

        // Calculate new average rating
        $newAverageRating = (($currentRating * $reviewCount) + $newRating) / ($reviewCount + 1);

        // Update rating and increment review count
        $stmt = $conn->prepare("UPDATE eventteams SET rating = ?, review_count = review_count + 1 WHERE team_id = ?");
        $stmt->bind_param("di", $newAverageRating, $teamId);
        if ($stmt->execute()) {
            header("Location: user_orders.php?success=Review submitted successfully!");
            exit();
        } else {
            header("Location: user_orders.php?error=Error updating rating. Please try again.");
            exit();
        }
    } else {
        header("Location: user_orders.php?error=Team not found.");
        exit();
    }
}

// Fetch all bookings for the logged-in user
$stmt = $conn->prepare("
    SELECT b.booking_id, b.event_id, b.team_id, b.event_date, b.people_attending, b.total_cost, b.payment_status, t.team_name, t.category, b.status
    FROM bookings b
    JOIN eventteams t ON b.team_id = t.team_id
    WHERE b.user_id = ? AND b.status != 'Cancelled'
");

$stmt->bind_param("i", $userId);
$stmt->execute();
$bookings = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Orders</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>

<div class="container">
    <h2>Your Bookings</h2>

    <!-- Display success or error message -->
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <!-- Display bookings in a table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Event Team</th>
                <th>Category</th>
                <th>Event Date</th>
                <th>Number of People</th>
                <th>Total Cost</th>
                <th>Payment Status</th>
                <th>Status</th>
                <th style="width:35%">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $bookings->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
                <td><?php echo htmlspecialchars($row['team_name']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                <td><?php echo ($row['category'] == 'Decoration') ? '-' : htmlspecialchars($row['people_attending']); ?></td>
                <td><?php echo htmlspecialchars($row['total_cost']); ?></td>
                <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td>
                <!-- Cancel Button -->
                <?php if ($row['status'] !== 'Cancelled' && $row['status'] !== 'Confirmed'&& $row['status'] !== 'Completed'): ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($row['booking_id']); ?>">
                        <button type="submit" name="cancel_booking" class="btn btn-danger">Cancel</button>
                    </form>
                <?php endif; ?>


                    <!-- Chat Button -->
                    <a href="chat.php?booking_id=<?php echo htmlspecialchars($row['booking_id']); ?>&team_id=<?php echo htmlspecialchars($row['team_id']); ?>" class="btn btn-info">Chat with Team</a>

                   <!-- Pay Now Button -->
                        <?php if ($row['status'] !== 'Cancelled' && $row['payment_status'] === 'Pending' && $row['total_cost'] !== NULL): ?>
                            <a href="payment.php?booking_id=<?php echo htmlspecialchars($row['booking_id']); ?>&amount=<?php echo htmlspecialchars($row['total_cost']); ?>" class="btn btn-success">Pay Now</a>
                        <?php endif; ?>


                </td>
            </tr>

<!-- Review Submission Form -->
<?php if ($row['status'] === 'Completed'): ?>
    <tr>
        <td colspan="9">
            <?php
            // Check if the user has already reviewed this team
            $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM reviews WHERE user_id = ? AND team_id = ?");
            $stmt->bind_param("ii", $userId, $row['team_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $reviewExists = $result->fetch_assoc()['count'] > 0;
            ?>

            <?php if (!$reviewExists): ?>
                <form action="review.php" method="POST" style="border: 1px solid #ddd; padding: 15px; border-radius: 5px; background-color: #f9f9f9;">
                    <input type="hidden" name="team_id" value="<?php echo $row['team_id']; ?>">
                    
                    <div class="form-group">
                        <label for="rating">Rating:</label>
                        <select name="rating" required class="form-control">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="comment">Review:</label>
                        <textarea name="comment" required class="form-control" rows="4"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            <?php else: ?>
                <div class="alert alert-warning">You have already submitted a review for this team.</div>
            <?php endif; ?>
        </td>
    </tr>
<?php endif; ?>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
