<?php


session_start();
include '../includes/db_connect.php'; // Include your database connection file
include 'header.php';

// Check if the admin is logged in
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

// Fetch complaints from the database
$sql = "
    SELECT c.complaint_id, u.username AS user_name, et.team_name, c.complaint_text, c.created_at 
    FROM complaints c
    JOIN users u ON c.user_id = u.user_id
    JOIN eventteams et ON c.team_id = et.team_id
    ORDER BY c.created_at DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Complaints</title>
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

<h2>Complaints List</h2>

<?php
if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Complaint ID</th><th>User</th><th>Team</th><th>Complaint</th><th>Date</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['complaint_id'] . "</td>";
        echo "<td>" . $row['user_name'] . "</td>";
        echo "<td>" . $row['team_name'] . "</td>";
        echo "<td>" . htmlspecialchars($row['complaint_text']) . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No complaints found.</p>";
}

$conn->close();
?>

</body>
</html>
