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

$userId = $_SESSION['userid'];
$message = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Fetch current password from the database
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if the current password is correct
    if (password_verify($currentPassword, $user['password'])) {
        // Validate new password
        if ($newPassword === $confirmPassword) {
            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update the password in the database
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt->bind_param("si", $hashedPassword, $userId);
            $stmt->execute();

            // Check if the update was successful
            if ($stmt->affected_rows > 0) {
                // Redirect to user dashboard after successful update
                header("Location: user_dashboard.php?message=Password changed successfully!");
                exit();
            } else {
                $message = "Failed to change the password. Please try again.";
            }
        } else {
            $message = "New password and confirmation do not match.";
        }
    } else {
        $message = "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="styles.css">
    <title>Change Password</title>
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 500px;
            margin: auto;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Change Password</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="current_password">Current Password</label>
            <input type="password" class="form-control" name="current_password" id="current_password" required>
        </div>
        <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" class="form-control" name="new_password" id="new_password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Change Password</button>
    </form>
</div>

</body>
</html>
