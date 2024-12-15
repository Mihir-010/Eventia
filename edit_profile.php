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
$stmt = $conn->prepare("SELECT * FROM eventteams WHERE team_id = ?");
$stmt->bind_param("i", $teamId);
$stmt->execute();
$result = $stmt->get_result();
$team = $result->fetch_assoc();

// Handle form submission for updating the profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teamName = $_POST['team_name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $contactInfo = $_POST['contact_info'];
    $minPrice = $_POST['min_price'];
    $maxPrice = $_POST['max_price'];
    $availabilityStatus = isset($_POST['availability_status']) ? 1 : 0;

    // Check if profile picture is uploaded
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $profilePic = basename($_FILES["profile_pic"]["name"]);
        $targetFile = $targetDir . $profilePic;
        move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFile);
    } else {
        // If no new file uploaded, keep the old one
        $profilePic = $team['profile_pic'];
    }

    // Update the team details
    $stmt = $conn->prepare("UPDATE eventteams SET team_name = ?, category = ?, description = ?, contact_info = ?, min_price = ?, max_price = ?, availability_status = ?, profile_pic = ? WHERE team_id = ?");
    $stmt->bind_param("ssssiiisi", $teamName, $category, $description, $contactInfo, $minPrice, $maxPrice, $availabilityStatus, $profilePic, $teamId);
    
    if ($stmt->execute()) {
        $successMsg = "Profile updated successfully.";
        // Refresh the team data after update
        $stmt = $conn->prepare("SELECT * FROM eventteams WHERE team_id = ?");
        $stmt->bind_param("i", $teamId);
        $stmt->execute();
        $result = $stmt->get_result();
        $team = $result->fetch_assoc();
    } else {
        $errorMsg = "Error updating profile. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <title>Edit Event Team Profile</title>
    <style>
       <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .form-table th, .form-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .form-table th {
            background-color: #f0f0f0;
        }

        .form-table input[type="text"],
        .form-table textarea,
        .form-table select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-table input[type="file"] {
            border: none;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #4CAF50;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .submit-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #218838;
        }

        .alert {
            padding: 10px;
            margin-bottom: 20px;
            color: white;
            border-radius: 5px;
            text-align: center;
        }

        .alert-success {
            background-color: #4CAF50;
        }

        .alert-error {
            background-color: #f44336;
        }
    </style>
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Event Team Profile</h2>

    <?php if (isset($successMsg)): ?>
        <div class="alert alert-success"><?php echo $successMsg; ?></div>
    <?php elseif (isset($errorMsg)): ?>
        <div class="alert alert-error"><?php echo $errorMsg; ?></div>
    <?php endif; ?>

    <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
        <table class="form-table">
            <tr>
                <th>Team Name</th>
                <td><input type="text" name="team_name" value="<?php echo htmlspecialchars($team['team_name']); ?>" required></td>
            </tr>
            <tr>
                <th>Category</th>
                <td>
                    <select name="category" required>
                        <option value="Catering" <?php if ($team['category'] == 'Catering') echo 'selected'; ?>>Catering</option>
                        <option value="Decoration" <?php if ($team['category'] == 'Decoration') echo 'selected'; ?>>Decoration</option>
                        <option value="Venue" <?php if ($team['category'] == 'Venue') echo 'selected'; ?>>Venue</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>Description</th>
                <td><textarea name="description" required><?php echo htmlspecialchars($team['description']); ?></textarea></td>
            </tr>
            <tr>
                <th>Contact Info</th>
                <td><input type="text" name="contact_info" value="<?php echo htmlspecialchars($team['contact_info']); ?>" required></td>
            </tr>
            <tr>
                <th>Minimum Price</th>
                <td><input type="number" name="min_price" value="<?php echo htmlspecialchars($team['min_price']); ?>" required></td>
            </tr>
            <tr>
                <th>Maximum Price</th>
                <td><input type="number" name="max_price" value="<?php echo htmlspecialchars($team['max_price']); ?>" required></td>
            </tr>
            <tr>
                <th>Profile Picture</th>
                <td><input type="file" name="profile_pic"><br>
                <?php if ($team['profile_pic']): ?>
                    <img src="uploads/<?php echo htmlspecialchars($team['profile_pic']); ?>" alt="Profile Picture" width="100">
                <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Availability Status</th>
                <td>
                    <label class="toggle-switch">
                        <input type="checkbox" name="availability_status" <?php if ($team['availability_status']) echo 'checked'; ?>>
                        <span class="slider"></span>
                    </label>
                </td>
            </tr>
        </table>

        <button type="submit" class="submit-btn">Update Profile</button>
    </form>
</div>

</body>
</html>

