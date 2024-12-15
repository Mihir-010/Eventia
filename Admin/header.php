

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
    </style>
</head>
<body>

    <div class="navbar">
        <div class="logo">
            <h2 style="color: white;">Admin Dashboard</h2>
        </div>
        <ul>
             <li><a href="admin_dashboard.php">Home</a></li>
            <li><a href="event_requests.php">Event Requests</a></li>
            <li><a href="view_review.php">View Reviews</a></li>
            <li><a href="view_complaints.php">View Complaints</a></li>
            <li><a href="bookings.php">Booking</a></li>
            <li><a href="users.php">Users</a></li>
            <li><a href="event_team_management.php">Team</a></li>
            <li><a href="logout.php" class="logout">Logout</a></li>
        </ul>
    </div>

    

</body>
</html>
