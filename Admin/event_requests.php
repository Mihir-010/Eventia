<?php
// Start the session
session_start();

// Verify if the admin is logged in; if not, redirect to the login page
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection
include 'C:\xampp\htdocs\Eventia\includes\db_connect.php';
include 'header.php';
// Handle Approve or Reject actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    
    // Determine the query based on the action type
    if ($action === 'approve') {
        $query = "UPDATE eventteams SET status = 'Approved' WHERE team_id = ?";
    } elseif ($action === 'reject') {
        $query = "UPDATE eventteams SET status = 'Rejected' WHERE team_id = ?";
    }

    // Prepare and execute the query
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Event team " . ($action === 'approve' ? 'approved' : 'rejected') . " successfully.');</script>";
    } else {
        echo "<script>alert('Error updating event team status.');</script>";
    }
    $stmt->close();
}

// Fetch all pending requests with additional details
$query = "SELECT team_id, team_name, category, description, contact_info, min_price, max_price, status 
          FROM eventteams WHERE status = 'Pending'";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Team Requests</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: teal;
            color: white;
        }
        .approve-btn, .reject-btn {
            padding: 5px 10px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
        }
        .approve-btn {
            background-color: green;
        }
        .reject-btn {
            background-color: red;
        }
    </style>
</head>
<body>

<h2>Pending Event Team Requests</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Category</th>
        <th>Description</th>
        <th>Contact Info</th>
        <th>Min Price</th>
        <th>Max Price</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['team_id']); ?></td>
                <td><?php echo htmlspecialchars($row['team_name']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars($row['contact_info']); ?></td>
                <td><?php echo htmlspecialchars($row['min_price']); ?></td>
                <td><?php echo htmlspecialchars($row['max_price']); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                <td>
                    <a href="event_requests.php?action=approve&id=<?php echo $row['team_id']; ?>" class="approve-btn">Approve</a>
                    <a href="event_requests.php?action=reject&id=<?php echo $row['team_id']; ?>" class="reject-btn">Reject</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="9">No pending event team requests.</td>
        </tr>
    <?php endif; ?>
</table>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
