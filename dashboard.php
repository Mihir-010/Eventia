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

// Fetch team details
$stmt = $conn->prepare("SELECT team_name, profile_pic FROM eventteams WHERE team_id = ?");
$stmt->bind_param("i", $teamId);
$stmt->execute();
$result = $stmt->get_result();
$team = $result->fetch_assoc();

// Fetch posts (include post_id in the query)
$stmt = $conn->prepare("SELECT post_id, title, description, image, created_at FROM event_team_post WHERE team_id = ?");
$stmt->bind_param("i", $teamId);
$stmt->execute();
$posts = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <title>Event Team Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            height: 100vh;
            width: 250px;
            background-color: #333;
            padding-top: 20px;
            position: fixed;
            top: 0;
            left: 0;
        }

        .sidebar a {
            padding: 15px;
            text-decoration: none;
            font-size: 18px;
            color: #fff;
            display: block;
        }

        .sidebar a:hover {
            background-color: #575757;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }

        .header {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: center;
            position: fixed;
            width: calc(100% - 10px);
            top: 0;
            z-index: 1;
        }

        .header h2 {
            margin: 0;
            color: white;
        }

        .profile-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-top: 60px; /* Adjust for fixed header */
        }

        .profile-section img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .team-name {
            font-size: 24px;
            font-weight: bold;
        }

        .main-content {
            margin-top: 20px;
        }

        .post-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .post-number {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .post-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .post-date {
            font-size: 12px;
            color: #777;
            margin-bottom: 15px;
        }

        .post-card img {
            max-width: 100%; /* Ensures the image doesn't exceed the card's width */
            width: 300px; /* Resizes the image to a specific width */
            height: auto; /* Maintains aspect ratio */
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .post-description {
            margin-bottom: 15px;
        }

        /* Button Styles */
        .post-buttons {
            text-align: center; /* Center the buttons */
            margin-top: 20px;
        }

        .edit-button, .delete-button {
            padding: 10px 15px;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            font-size: 14px;
        }

        .edit-button {
            background-color: #28a745; /* Green for edit */
            margin-right: 10px;
        }

        .edit-button:hover {
            background-color: #218838;
        }

        .delete-button {
            background-color: #dc3545; /* Red for delete */
        }

        .delete-button:hover {
            background-color: #c82333;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: -20px;
            position: fixed;
            width: calc(100% - .25px);
            bottom: 0;

        }

        .add-post-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .add-post-button:hover {
            background-color: #0056b3;
        }
        
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <br>
        <br>
        <a href="dashboard.php">Dashboard</a>
        <a href="post_work.php">Post Work</a>
        <a href="team_orders.php">Orders</a>
        <a href="edit_profile.php">Edit Profile</a>
        <a href="change_password_event_team.php">Change Password</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Header -->
    <div class="header">
        <h2>Welcome to Event Team Dashboard</h2>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="profile-section">
            <div class="team-name">
                <?php echo htmlspecialchars($team['team_name']); ?>
            </div>
            <div>
            <img src="uploads/<?php echo htmlspecialchars($team['profile_pic']); ?>" alt="Profile Picture">
            </div>
        </div>

        <div class="main-content">
            <a href="post_work.php" class="add-post-button">Add New Post</a>
            <h3>Recent Posts</h3>

            <?php 
            $postNumber = 1; // Initialize post number counter
            while ($post = $posts->fetch_assoc()): 
            ?>
                <div class="post-card">
                    <div class="post-number">Post #<?php echo $postNumber++; ?></div>
                    <div class="post-title"><?php echo htmlspecialchars($post['title']); ?></div>
                    <div class="post-date"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></div>
                    <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image">
                    <div class="post-description"><?php echo htmlspecialchars($post['description']); ?></div>

                    <!-- Edit and Delete buttons centered -->
                    <div class="post-buttons">
                        <a href="edit_post.php?post_id=<?php echo $post['post_id']; ?>" class="edit-button">Edit</a>
                        <a href="delete_post.php?post_id=<?php echo $post['post_id']; ?>" class="delete-button" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                    </div>
                </div>
            <?php endwhile; ?>

        </div>
    </div>

    <!-- Footer -->

    <footer>
        <p>&copy; 2024 Eventia - All Rights Reserved</p>
    </footer>

</body>
</html>
