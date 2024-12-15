<?php
// Start session
session_start();

// Include database connection
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $contact_info = $_POST['contact_info']; // Assuming contact_info is the phone number or similar field

    // Check if email and contact_info match in eventteams table
    $stmt = $conn->prepare("SELECT team_id FROM eventteams WHERE email = ? AND contact_info = ?");
    $stmt->bind_param("ss", $email, $contact_info);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['teamid'] = $result->fetch_assoc()['team_id'];
        header("Location: reset_password_event_team.php");
        exit();
    } else {
        $error = "No event team account found with that email and contact information.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password - Event Team</title>
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
        <h2>Forgot Password - Event Team</h2>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="text" name="contact_info" placeholder="Enter your contact information" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
