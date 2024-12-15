<?php
// Start session
session_start();

// Include database connection
require_once 'includes/db_connect.php';

// Handle form submission for registration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teamName = $_POST['team_name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $contactInfo = $_POST['contact_info'];
    $minPrice = $_POST['min_price'];
    $maxPrice = $_POST['max_price'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing the password

    // Check if profile picture is uploaded
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $profilePic = basename($_FILES["profile_pic"]["name"]);
        $targetFile = $targetDir . $profilePic;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFile)) {
            // Insert the team details into the database
            $stmt = $conn->prepare("INSERT INTO eventteams (team_name, category, description, contact_info, min_price, max_price, profile_pic, email, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssissss", $teamName, $category, $description, $contactInfo, $minPrice, $maxPrice, $profilePic, $email, $password);
            
             // Execute the statement
     if ($stmt->execute()) {
        // Redirect to the login page on successful registration
        header("Location: login_event_team.php");
        exit();
    } else {
        // Capture database error
        $error = "Registration failed: " . $stmt->error;
    }
        } else {
            $errorMsg = "Error uploading profile picture. Please try again.";
        }
    } else {
        $errorMsg = "No profile picture uploaded.";
    }
    
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            margin: 0;
            padding: -20px;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
            font-weight: bold;
            text-transform: uppercase;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-field {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 550px;
            margin-bottom: 10px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-field label {
            font-size: 14px;
            color: #333;
            font-weight: bold;
            margin-bottom: 2px;
        }

        textarea {
            resize: vertical;
            height: 80px;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        .password-field {
            position: relative;
            width: 100%;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px; /* Adjust as needed */
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>

    <title>Event Team Registration</title>
</head>
<body>
    <div class="container">
        <h2>Register Event Team</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="register_event_team.php" method="POST" enctype="multipart/form-data">
    <table class="form-table">
        <tr>
            <th>Team Name</th>
            <td><input type="text" name="team_name" required></td>
        </tr>
        <tr>
            <th>Category</th>
            <td>
                <select name="category" required>
                    <option value="Catering">Catering</option>
                    <option value="Decoration">Decoration</option>
                    <option value="Venue">Venue</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>Description</th>
            <td><textarea name="description" required></textarea></td>
        </tr>
        <tr>
            <th>Contact Info</th>
            <td><input type="text" name="contact_info" required></td>
        </tr>
        <tr>
            <th>Minimum Price</th>
            <td><input type="number" name="min_price" required></td>
        </tr>
        <tr>
            <th>Maximum Price</th>
            <td><input type="number" name="max_price" required></td>
        </tr>
        <tr>
            <th>Profile Picture</th>
            <td><input type="file" name="profile_pic" accept="image/*" required></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><input type="email" name="email" required></td>
        </tr>
        <tr>
            <th>Password</th>
            <td><input type="password" name="password" required></td>
        </tr>
    </table>
    <button type="submit" class="submit-btn">Register</button>
</form>


        <p>Already registered? <a href="login_event_team.php">Log In</a></p>
    </div>

    <script>
        function validatePrices() {
            const minPrice = parseFloat(document.getElementById('min_price').value);
            const maxPrice = parseFloat(document.getElementById('max_price').value);

            if (minPrice >= maxPrice) {
                alert("Maximum price must be higher than minimum price.");
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const passwordFieldType = passwordInput.type;

            if (passwordFieldType === 'password') {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        }
    </script>
</body>
</html>
