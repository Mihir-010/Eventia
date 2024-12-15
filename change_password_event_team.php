<?php
// Start session
session_start();

// Include database connection
require_once 'includes/db_connect.php';

// Check if the event team is logged in
if (!isset($_SESSION['team_id'])) {
    header("Location: login_event_team.php");
    exit();
}

$teamId = $_SESSION['team_id'];
$errorMsg = '';
$successMsg = '';

// Fetch current password from the database
$stmt = $conn->prepare("SELECT password FROM eventteams WHERE team_id = ?");
$stmt->bind_param("i", $teamId);
$stmt->execute();
$result = $stmt->get_result();
$team = $result->fetch_assoc();
$currentPasswordHash = $team['password'];

// Handle form submission for password reset
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Form validation
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $errorMsg = "All fields are required.";
    } elseif (!password_verify($currentPassword, $currentPasswordHash)) {
        $errorMsg = "Current password is incorrect.";
    } elseif ($newPassword !== $confirmPassword) {
        $errorMsg = "New password and confirm password do not match.";
    } elseif (strlen($newPassword) < 8) {
        $errorMsg = "New password must be at least 8 characters long.";
    } else {
        // Hash the new password
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update password in the database
        $stmt = $conn->prepare("UPDATE eventteams SET password = ? WHERE team_id = ?");
        $stmt->bind_param("si", $newPasswordHash, $teamId);

        if ($stmt->execute()) {
            $successMsg = "Password updated successfully.";
        } else {
            $errorMsg = "Error updating password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative; /* For positioning the eye icon */
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input[type="password"] {
            width: 100%;
            padding: 8px;
            padding-right: 40px; /* Add space for the eye icon */
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .eye-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%); /* Center vertically */
            cursor: pointer;
            font-size: 16px;
            color: #555;
        }

        .submit-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #218838;
        }

        .alert {
            padding: 10px;
            margin-bottom: 20px;
            color: white;
            border-radius: 5px;
            text-align: center;
        }

        .alert-success {
            background-color: #4CAF50;
        }

        .alert-error {
            background-color: #f44336;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Reset Password</h2>

    <?php if (!empty($errorMsg)): ?>
        <div class="alert alert-error"><?php echo $errorMsg; ?></div>
    <?php elseif (!empty($successMsg)): ?>
        <div class="alert alert-success"><?php echo $successMsg; ?></div>
    <?php endif; ?>

    <form action="reset_password_event_team.php" method="POST" id="reset-password-form">
        <div class="form-group">
            <label for="current_password">Current Password:</label>
            <input type="password" name="current_password" id="current_password" required>
            <span class="eye-icon" id="toggleCurrentPassword">👁️</span>
        </div>

        <div class="form-group">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" id="new_password" required>
            <span class="eye-icon" id="toggleNewPassword">👁️</span>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>
            <span class="eye-icon" id="toggleConfirmPassword">👁️</span>
        </div>

        <button type="submit" class="submit-btn">Reset Password</button>
    </form>
</div>

<script>
// Client-side form validation
document.getElementById('reset-password-form').addEventListener('submit', function(event) {
    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;

    // Password length validation
    if (newPassword.length < 8) {
        alert('New password must be at least 8 characters long.');
        event.preventDefault(); // Stop form submission
    }

    // Check if new password matches confirm password
    if (newPassword !== confirmPassword) {
        alert('New password and confirm password do not match.');
        event.preventDefault(); // Stop form submission
    }
});

// Toggle password visibility
function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = '🙈'; // Change to an open eye icon
    } else {
        input.type = 'password';
        icon.textContent = '👁️'; // Change back to a closed eye icon
    }
}

// Add event listeners for toggling passwords
document.getElementById('toggleCurrentPassword').addEventListener('click', function() {
    togglePasswordVisibility('current_password', 'toggleCurrentPassword');
});

document.getElementById('toggleNewPassword').addEventListener('click', function() {
    togglePasswordVisibility('new_password', 'toggleNewPassword');
});

document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
    togglePasswordVisibility('confirm_password', 'toggleConfirmPassword');
});
</script>

</body>
</html>
