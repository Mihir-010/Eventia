<?php
// Start session
session_start();

// Include database connection
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Check if email and phone match
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND phone = ?");
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['userid'] = $result->fetch_assoc()['user_id'];
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "No account found with that email and phone number.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password</title>
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
        <h2>Forgot Password</h2>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="text" name="phone" placeholder="Enter your phone number" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
