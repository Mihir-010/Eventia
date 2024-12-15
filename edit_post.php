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

$teamId = $_SESSION['teamid'];

// Check if post_id is provided in the URL
if (!isset($_GET['post_id'])) {
    header("Location: dashboard.php");
    exit();
}

$postId = $_GET['post_id'];

// Fetch the post details
$stmt = $conn->prepare("SELECT title, description, image FROM event_team_post WHERE post_id = ? AND team_id = ?");
$stmt->bind_param("ii", $postId, $teamId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Post not found.";
    exit();
}

$post = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $updatedImage = $post['image']; // Keep the current image as the default

    // Check if a new image was uploaded
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $imageName = basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $imageName;
        $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Check if the file is an image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                $updatedImage = $imageName; // Use the new image name

                // Optionally, delete the old image if a new one is uploaded
                if ($post['image'] != "" && file_exists("uploads/" . $post['image'])) {
                    unlink("uploads/" . $post['image']); // Delete the old image
                }
            } else {
                echo "Error uploading image.";
            }
        } else {
            echo "File is not an image.";
        }
    }

    // Update the post in the database
    $stmt = $conn->prepare("UPDATE event_team_post SET title = ?, description = ?, image = ? WHERE post_id = ? AND team_id = ?");
    $stmt->bind_param("sssii", $title, $description, $updatedImage, $postId, $teamId);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error updating the post.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Edit Post</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
        }

        label {
            font-size: 18px;
            margin-bottom: 5px;
            display: block;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="file"] {
            margin-bottom: 20px;
        }

        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .current-image {
            margin-bottom: 20px;
        }

        .current-image img {
            max-width: 300px;
            border-radius: 8px;
        }
    </style>
</head>
<body>

    <h2>Edit Post</h2>
    <div class="container">
    <form action="edit_post.php?post_id=<?php echo $postId; ?>" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" rows="5" required><?php echo htmlspecialchars($post['description']); ?></textarea>

        <div class="current-image">
            <label>Current Image:</label>
            <?php if ($post['image']): ?>
                <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image">
            <?php else: ?>
                <p>No image available.</p>
            <?php endif; ?>
        </div>

        <label for="image">Upload New Image (optional):</label>
        <input type="file" name="image" id="image">

        <button type="submit">Update Post</button>
    </form>
    </div>

</body>
</html>
