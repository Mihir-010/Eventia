<?php
// Start session
session_start();

// Include database connection
require_once 'includes/db_connect.php';

// Check if the event team is logged in
if (!isset($_SESSION['teamid'])) {
    header("Location: login_event_team.php");
    exit();
}

// Fetch the team ID from the session
$teamId = $_SESSION['teamid'];

// Initialize variables for errors or success messages
$error = '';
$success = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Check if file is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $fileName = $_FILES['image']['name'];
        $fileTmpName = $_FILES['image']['tmp_name'];
        $fileSize = $_FILES['image']['size'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Check if the file extension is allowed
        if (in_array($fileExt, $allowed)) {
            // Define the target directory for uploads
            $uploadDir = 'uploads/';
            $uniqueFileName = uniqid() . '.' . $fileExt;
            $targetFile = $uploadDir . $uniqueFileName;

            // Move the uploaded file to the uploads directory
            if (move_uploaded_file($fileTmpName, $targetFile)) {
                // Prepare the SQL statement to insert post data into the event_team_post table
                $stmt = $conn->prepare("INSERT INTO event_team_post (team_id, title, description, image) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $teamId, $title, $description, $uniqueFileName);
                
                if ($stmt->execute()) {
                    // Post added successfully, redirect to dashboard
                    header("Location: dashboard.php");
                    exit(); // Make sure the script stops after redirection
                } else {
                    $error = "Failed to insert post into the database.";
                }
            } else {
                $error = "Error moving the uploaded file.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG, & GIF files are allowed.";
        }
    } else {
        $error = "Please upload an image.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Post Your Work</title>
    <style>
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
            border-radius: 8px;
        }

        .container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input, 
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-group textarea {
            resize: vertical;
        }

        .form-group input[type="file"] {
            padding: 0;
        }

        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #218838;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        .success {
            color: green;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Post Your Recent Work</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form action="post_work.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="5" required></textarea>
            </div>

            

            <div class="form-group">
                <label for="image">Upload Image:</label>
                <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.gif" required>
            </div>

            <div class="form-group">
                <button type="submit">Submit Post</button>
            </div>
        </form>
    </div>

</body>
</html>
