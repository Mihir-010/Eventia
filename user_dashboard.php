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

// Fetch user details from the database
$userId = $_SESSION['userid'];
$stmt = $conn->prepare("SELECT username, email, phone FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle form submission to update user details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Update user details in the database
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, phone = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $username, $email, $phone, $userId);
    
    if ($stmt->execute()) {
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="shortcut icon" href="images/favicon.png" type="">
    <title>User Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <link href="css/font-awesome.min.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" />
    <style>
        .sidebar {
            width: 200px;
            background-color: black; /* Changed to black */
            padding: 15px;
            position: fixed;
            height: 100%;
            top: 0;
            left: 0;
            color: white; /* Text color for sidebar */
        }
        .h1{
            color: white;
        }
        .sidebar a {
            color: white; /* Link color */
        }
        .sidebar a:hover {
            color: yellow; /* Change color on hover */
        }
        .content {
            margin-left: 220px; /* Space for the sidebar */
            padding: 20px;
        }
        .header {
            background-color: black;
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between; /* Space between header items */
            align-items: center; /* Align items vertically center */
        }
        .header h1 {
            margin: 0; /* Remove margin for header title */
            flex: 1; /* Allow the title to take available space */
        }
        .header nav {
            flex: 1; /* Allow the nav to take available space */
            text-align: center; /* Center the navigation */
        }
        .header a {
            color: white; /* Header link color */
            transition: color 0.3s; /* Smooth color transition */
        }
        .header a:hover {
            color: yellow; /* Change color on hover */
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4>User Menu</h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="user_dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="user_orders.php">Orders</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="change_user_password.php">Change Password</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="complain.php">Complaints</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Log out</a>
            </li>
        </ul>
    </div>

    <div class="content">
        <h2>User Dashboard</h2>

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

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <form method="POST" action="">
                    <tr>
                        <td>Username</td>
                        <td><input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required class="form-control"></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td><input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="form-control"></td>
                    </tr>
                    <tr>
                        <td>Phone</td>
                        <td><input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-center">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </td>
                    </tr>
                </form>
            </tbody>
        </table>
    </div>
</body>
</html>

