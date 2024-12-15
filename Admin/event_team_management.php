<?php
// Start the session
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

include 'C:\xampp\htdocs\Eventia\includes\db_connect.php';
include 'header.php';

// Handle deletion of event team
if (isset($_POST['delete_team'])) {
    $team_id = intval($_POST['team_id']);

    // First, delete all related reviews (if any)
    $deleteReviewsQuery = "DELETE FROM reviews WHERE team_id = ?";
    if ($stmtDeleteReviews = $conn->prepare($deleteReviewsQuery)) {
        $stmtDeleteReviews->bind_param("i", $team_id);
        $stmtDeleteReviews->execute();
        $stmtDeleteReviews->close();
    }

    // Now delete the event team
    $deleteTeamQuery = "DELETE FROM eventteams WHERE team_id = ?";
    if ($stmtDeleteTeam = $conn->prepare($deleteTeamQuery)) {
        $stmtDeleteTeam->bind_param("i", $team_id);
        if ($stmtDeleteTeam->execute()) {
            $success_message = "Event team deleted successfully!";
        } else {
            $error_message = "Error deleting event team. Please try again.";
        }
        $stmtDeleteTeam->close();
    }
}

// Fetch all event teams
$query = "SELECT * FROM eventteams"; 
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Team Management</title>
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
        .add-btn {
            background-color: green;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>

<div class="container">
    <br><br><br>
    <h2><center>Event Team Management</center></h2>
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

    <!-- Display event teams in a table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Team ID</th>
                <th>Team Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Contact Info</th>
                <th>Email</th>
                <th>Rating</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['team_id']; ?></td>
                <td><?php echo htmlspecialchars($row['team_name']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars($row['contact_info']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['rating']); ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="team_id" value="<?php echo $row['team_id']; ?>">
                        <button type="submit" name="delete_team" class="delete-btn" onclick="return confirm('Are you sure you want to delete this team?');">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<br>
<div style="text-align: center; margin-bottom: 20px;">
    <a href="add_event_team.php" class="add-btn">Add Event Team</a>
</div>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
