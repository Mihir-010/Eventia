<?php
// Start session
session_start();

// Include database connection
require_once 'includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details
$userId = $_SESSION['userid'];
$stmt = $conn->prepare("SELECT username, email, phone FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    $error = "User not found.";
}

// Update user details if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Prepare update statement
    $updateStmt = $conn->prepare("UPDATE users SET username = ?, email = ?, phone = ? WHERE user_id = ?");
    $updateStmt->bind_param("sssi", $username, $email, $phone, $userId);
    
    if ($updateStmt->execute()) {
        $success = "Details updated successfully.";
        // Refresh user data
        $user['username'] = $username;
        $user['email'] = $email;
        $user['phone'] = $phone;
    } else {
        $error = "Failed to update details. Please try again.";
    }
}

// Logout logic
if (isset($_POST['logout'])) {
    // Clear session variables if they exist
    if (isset($_SESSION['fosuid'])) {
        unset($_SESSION['fosuid']);
    }

    session_destroy();
    // Redirect to index.php after logout
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Profile</title>
</head>
<body>
    <div class="container">
        <h2>Your Profile</h2>

        <!-- Display success or error message -->
        <?php if (isset($success)): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" value="<?php echo isset($user) ? htmlspecialchars($user['username']) : ''; ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?php echo isset($user) ? htmlspecialchars($user['email']) : ''; ?>" required>
            <input type="text" name="phone" placeholder="Phone" value="<?php echo isset($user) ? htmlspecialchars($user['phone']) : ''; ?>" required>
            <button type="submit" name="update">Update</button>
        </form>

        <form method="post" action="">
            <button type="submit" name="logout">Log Out</button>
        </form>
    </div>
</body>
</html>
