<?php
// Start the session
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['id'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

include 'C:\xampp\htdocs\Eventia\includes\db_connect.php';

// Initialize variables to store form data and error messages
$username = $email = $phone = $password = '';
$error = '';
$success = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize input
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    
    // Basic validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if the email already exists
        $checkEmailQuery = "SELECT email FROM users WHERE email = ?";
        $stmt = $conn->prepare($checkEmailQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "This email is already registered.";
        } else {
            // Check if the username already exists
            $checkUsernameQuery = "SELECT username FROM users WHERE username = ?";
            $stmt = $conn->prepare($checkUsernameQuery);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "This username is already taken.";
            } else {
                // Username and email are not taken, proceed with registration
                $stmt->close();

                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert the user into the database
                $insertQuery = "INSERT INTO users (username, email, phone, password, created_at) VALUES (?, ?, ?, ?, NOW())";
                $stmt = $conn->prepare($insertQuery);
                $stmt->bind_param("ssss", $username, $email, $phone, $hashed_password);

                if ($stmt->execute()) {
                    // Success: show message and redirect
                    echo "<script>alert('User added successfully'); window.location='users.php';</script>";
                    exit();
                } else {
                    $error = "Error adding user: " . $conn->error;
                }
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: teal;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            margin-bottom: 10px;
        }
        .submit-btn {
            padding: 10px;
            background-color: teal;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        .submit-btn:hover {
            background-color: darkcyan;
        }
        .back-btn {
            display: block;
            margin-top: 15px;
            text-align: center;
            color: teal;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Add New User</h2>

    <!-- Display error or success message -->
    <?php if (!empty($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>

    <form action="add_user.php" method="post">
        <label for="username">Username *</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
        
        <label for="email">Email *</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        
        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
        
        <label for="password">Password *</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="submit-btn">Add User</button>
    </form>

    <a href="users.php" class="back-btn">Back to User Management</a>
</div>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
