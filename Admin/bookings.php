<?php
// Start the session
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

include 'C:\xampp\htdocs\Eventia\includes/db_connect.php';
include 'header.php';
// Handle deletion of booking
if (isset($_POST['delete_booking'])) {
    $booking_id = intval($_POST['booking_id']);
    $deleteQuery = "DELETE FROM bookings WHERE booking_id = ?";
    $stmtDelete = $conn->prepare($deleteQuery);
    $stmtDelete->bind_param("i", $booking_id);

    if ($stmtDelete->execute()) {
        $success_message = "Booking deleted successfully!";
    } else {
        $error_message = "Error deleting booking. Please try again.";
    }
}

// Fetch all bookings
$query = "SELECT * FROM bookings";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings Management</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: teal;
            color: white;
        }
        .edit-btn, .delete-btn {
            padding: 5px 10px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
        }
        .edit-btn {
            background-color: blue;
        }
        .delete-btn {
            background-color: red;
        }
    </style>
</head>
<body>

<div class="container">
    <br><br><br>
    <h2><center>Bookings Management</center></h2>
    <br><br><br>
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
                <th>User ID</th>
                <th>Team ID</th>
                <th>Event Date</th>
                <th>Number of People</th>
                <th>Payment Status</th>
                <th>Status</th>
                <th>Total Cost</th>

            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['booking_id']; ?></td>
                <td><?php echo $row['user_id']; ?></td>
                <td><?php echo $row['team_id']; ?></td>
                <td><?php echo $row['event_date']; ?></td>
                <td><?php echo $row['people_attending']; ?></td>
                <td><?php echo $row['payment_status']; ?></td>
                <td><?php echo $row['status']; ?></td> <!-- Added Status -->
                <td><?php echo $row['total_cost']; ?></td> <!-- Added Total Cost -->
                
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
