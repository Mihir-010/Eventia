<?php
// Start session
session_start();

// Include database connection
require_once 'C:\xampp\htdocs\Eventia\includes\db_connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL statement to check admin credentials
    $stmt = $conn->prepare("SELECT id, password FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($adminId, $hashedPassword);
    
    if ($stmt->fetch()) {
        // Verify password
        if (password_verify($password, $hashedPassword)) {
            // Password is correct, store session and redirect to admin dashboard
            $_SESSION['id'] = $adminId;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "No admin found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Admin Login</title>
</head>
<body>
    <div class="container">
        <h2>Admin Login</h2>

        <!-- Display error message if login fails -->
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <!-- Login form -->
        <form method="post" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Log In</button>
        </form>
    </div>
</body>
</html>
