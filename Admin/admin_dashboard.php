<?php
// Start the session
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['id'])) {
    // Redirect to the login page if not logged in or not an admin
    header("Location: index.php");
    exit();
}

// Include database connection
include '../includes/db_connect.php';
include 'header.php';
// Fetch data for statistics
$user_count_query = "SELECT COUNT(*) AS user_count FROM users";
$team_count_query = "SELECT COUNT(*) AS team_count FROM eventteams WHERE status = 'Approved'";
$pending_teams_query = "SELECT COUNT(*) AS pending_teams FROM eventteams WHERE status = 'Pending'";
$booking_count_query = "SELECT COUNT(*) AS booking_count FROM bookings";

$user_count = $conn->query($user_count_query)->fetch_assoc()['user_count'];
$team_count = $conn->query($team_count_query)->fetch_assoc()['team_count'];
$pending_teams = $conn->query($pending_teams_query)->fetch_assoc()['pending_teams'];
$booking_count = $conn->query($booking_count_query)->fetch_assoc()['booking_count'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: #f4f4f4;
        }
        .navbar {
            background-color: teal;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar ul {
            list-style: none;
            display: flex;
        }
        .navbar ul li {
            margin-left: 20px;
        }
        .navbar ul li a {
            text-decoration: none;
            color: white;
            font-size: 16px;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        .navbar ul li a:hover {
            color: #f0c14b;
        }
        .logout {
            background-color: #ff4d4d;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .logout:hover {
            background-color: #ff3333;
        }
        .content {
            padding: 20px;
            text-align: center;
        }
        .dashboard-grid {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin: 20px 0;
        }
        .card {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            flex: 1;
            max-width: 250px;
            text-align: center;
        }
        .card h3 {
            margin-bottom: 10px;
        }
        .card p {
            font-size: 24px;
            margin: 0;
        }
        .actions {
            margin-top: 20px;
        }
        .actions button {
            background-color: teal;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px;
        }
        .actions button:hover {
            background-color: darkcyan;
        }
    </style>
</head>
<body>

    
    <div class="content">
        <h1>Welcome, Admin!</h1>
        <p>Manage your platform using the options below or view the latest stats.</p>

        <div class="dashboard-grid">
            <div class="card">
                <h3>Total Users</h3>
                <p><?php echo $user_count; ?></p>
            </div>

            <div class="card">
                <h3>Approved Event Teams</h3>
                <p><?php echo $team_count; ?></p>
            </div>

            <div class="card">
                <h3>Pending Event Teams</h3>
                <p><?php echo $pending_teams; ?></p>
            </div>

            <div class="card">
                <h3>Total Bookings</h3>
                <p><?php echo $booking_count; ?></p>
            </div>
        </div>

        <div class="actions">
            <button onclick="window.location.href='users.php'">Manage Users</button>
            <button onclick="window.location.href='event_team_management.php'">Manage Event Teams</button>
            <button onclick="window.location.href='bookings.php'">Manage Bookings</button>
        </div>
    </div>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
