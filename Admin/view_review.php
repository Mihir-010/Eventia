<?php
// Start the session
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

include 'C:\xampp\htdocs\Eventia\includes/db_connect.php';
include 'header.php';

// Prepare the SQL statement to fetch all reviews
$stmt = $conn->prepare("
    SELECT r.review_id, r.user_id, r.rating, r.comment, u.username, t.team_name
    FROM reviews r
    JOIN users u ON r.user_id = u.user_id
    JOIN eventteams t ON r.team_id = t.team_id
");

// Execute the statement
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews Management</title>
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
    </style>
</head>
<body>

<div class="container">
    <br><br><br>
    <h2><center>Reviews Management</center></h2>
    <br><br><br>
    <!-- Display reviews in a table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Review ID</th>
                <th>User</th>
                <th>Team</th>
                <th>Rating</th>
                <th>Review</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['review_id']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['team_name']); ?></td>
                <td><?php echo htmlspecialchars($row['rating']); ?></td>
                <td><?php echo htmlspecialchars($row['comment']); ?></td>
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
