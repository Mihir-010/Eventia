<?php
// Start session
session_start();

// Include the database connection
require_once 'includes/db_connect.php';

// Check if the event team is logged in
if (!isset($_SESSION['teamid'])) {
    header("Location: login_event_team.php");
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Check if passwords match
    if ($newPassword === $confirmPassword) {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Prepare SQL statement to update the password
        $teamId = $_SESSION['teamid'];
        $stmt = $conn->prepare("UPDATE eventteams SET password = ? WHERE team_id = ?");
        $stmt->bind_param("si", $hashedPassword, $teamId);
        
        // Execute the statement
        if ($stmt->execute()) {
            // Password reset successful
            session_destroy(); // End session after password reset
            header("Location: login_event_team.php");
            exit();
        } else {
            $error = "Error updating password. Please try again.";
        }
    } else {
        $error = "Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <title>Reset Password</title>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>

        <!-- Display error message if password reset fails -->
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <div class="password-wrapper">
                <input type="password" name="new_password" id="new_password" placeholder="Enter new password" required>
                <i class="fas fa-eye" id="toggleNewPassword" onclick="togglePasswordVisibility('new_password', 'toggleNewPassword')"></i>
            </div>
            <div class="password-wrapper">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required>
                <i class="fas fa-eye" id="toggleConfirmPassword" onclick="togglePasswordVisibility('confirm_password', 'toggleConfirmPassword')"></i>
            </div>
            <button type="submit">Reset Password</button>
        </form>
    </div>

    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</body>
</html>
