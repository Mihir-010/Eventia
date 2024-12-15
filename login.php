<?php
// Start session
session_start();

// Include the database connection
require_once 'includes/db_connect.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['user_password'] ?? '';

    if (!empty($email) && !empty($password)) {
        // Prepare SQL query to fetch user details
        $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['userid'] = $user['user_id'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Please fill in both email and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
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
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="post" action="">
            <input type="email" name="email" placeholder="Enter your email" required>
            <div class="password-wrapper">
                <input type="password" name="user_password" id="login_password" placeholder="Enter your password" required>
                <i class="fas fa-eye" id="toggleLoginPassword" onclick="togglePasswordVisibility('login_password', 'toggleLoginPassword')"></i>
            </div>
            <button type="submit">Log In</button>
        </form>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <p>Forgot your password? <a href="forgot.php">Click here</a></p>
        <p>New user? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
