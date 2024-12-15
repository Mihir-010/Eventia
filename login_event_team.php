<?php
// Start session and include database connection
session_start();
require_once 'includes/db_connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL statement to retrieve team data
    $stmt = $conn->prepare("SELECT team_id, password, status FROM eventteams WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($teamId, $hashedPassword, $status);
    
    if ($stmt->fetch()) {
        // Verify the password
        if (password_verify($password, $hashedPassword)) {
            // Check team status
            if ($status === 'Approved') {
                $_SESSION['teamid'] = $teamId; // Store team ID in session

                // Debugging output to check session and redirection
                echo "Login successful. Team ID: " . $_SESSION['teamid'];
                echo "Redirecting to dashboard...";

                // Use exit to ensure no further code is executed
                header("Location: dashboard.php");
                exit();
            } elseif ($status === 'Pending') {
                $error = "Your registration is under review. Please wait for admin approval.";
            } elseif ($status === 'Rejected') {
                $error = "Your registration was rejected by the admin.";
            }
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Event Team Login</title>
</head>
<body>
    <div class="container">
        <h2>Event Team Login</h2>

        <!-- Display error message if login fails -->
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Log In</button>
        </form>

        <p>Not registered? <a href="register_event_team.php">Register</a></p>
        <p>Forgot your password? <a href="forgot_event_team.php">Click here</a></p>
    </div>
</body>
</html>
