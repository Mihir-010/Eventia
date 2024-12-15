<?php
// Start the session
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

include 'C:\xampp\htdocs\Eventia\includes\db_connect.php';
include 'header.php';

// Initialize variables
$team_name = $category = $description = $contact_info = $availability_status = $rating = $status = $email = $password = $profile_pic = "";
$min_price = $max_price = 0.00; 
$team_name_err = $email_err = $password_err = $success_message = $profile_pic_err = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate team name
    if (empty(trim($_POST["team_name"]))) {
        $team_name_err = "Please enter the team name.";
    } else {
        $team_name = trim($_POST["team_name"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
    } else {
        // Check if email already exists
        $sql = "SELECT team_id FROM event_team WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_email);
            $param_email = trim($_POST["email"]);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $email_err = "This email is already taken.";
                } else {
                    $email = trim($_POST["email"]);
                }
            }
            $stmt->close();
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT); // Hash password
    }

    // Additional fields
    $category = $_POST["category"];
    $description = $_POST["description"];
    $contact_info = $_POST["contact_info"];
    $availability_status = isset($_POST["availability_status"]) ? 1 : 0; // Checkbox
    $rating = $_POST["rating"];
    $status = $_POST["status"];
    $min_price = $_POST["min_price"];
    $max_price = $_POST["max_price"];

    // Profile picture upload
    if (!empty($_FILES["profile_pic"]["name"])) {
        $target_dir = "../uploads/";

        // Check if the uploads directory exists
        if (!is_dir($target_dir)) {
            echo "The uploads directory does not exist.";
            exit();
        }

        $profile_pic = $target_dir . basename($_FILES["profile_pic"]["name"]);
        $imageFileType = strtolower(pathinfo($profile_pic, PATHINFO_EXTENSION));

        // Check if file is an image
        $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
        if ($check === false) {
            $profile_pic_err = "File is not an image.";
        }

        // Check file size (limit to 2MB)
        if ($_FILES["profile_pic"]["size"] > 2000000) {
            $profile_pic_err = "Sorry, your file is too large.";
        }

        // Allow certain file formats
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
            $profile_pic_err = "Sorry, only JPG, JPEG, and PNG files are allowed.";
        }

        // If no errors, move the file to the target directory
        if (empty($profile_pic_err) && move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $profile_pic)) {
            // File uploaded successfully
        } else {
            $profile_pic_err = "Sorry, there was an error uploading your file.";
        }
    }

    // If there are no errors, insert the new event team into the database
    if (empty($team_name_err) && empty($email_err) && empty($password_err) && empty($profile_pic_err)) {
        $sql = "INSERT INTO event_team (team_name, category, description, contact_info, availability_status, rating, status, profile_pic, email, password, min_price, max_price) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            // Bind the parameters
            $stmt->bind_param("ssssiissssdd", $param_team_name, $param_category, $param_description, $param_contact_info, $param_availability_status, $param_rating, $param_status, $param_profile_pic, $param_email, $param_password, $param_min_price, $param_max_price);

            // Set parameters
            $param_team_name = $team_name;
            $param_category = $category;
            $param_description = $description;
            $param_contact_info = $contact_info;
            $param_availability_status = $availability_status;
            $param_rating = $rating; // Ensure it's a float
            $param_status = $status;
            $param_profile_pic = $profile_pic;
            $param_email = $email;
            $param_password = $password;
            $param_min_price = $min_price; // Ensure it's a float
            $param_max_price = $max_price; // Ensure it's a float

            // Execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to event_team_management.php after successful addition
                $success_message = "Event team added successfully!";
                header("refresh:2;url=event_team_management.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }

    // Close connection
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event Team</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-control:focus {
            border-color: #007bff;
            outline: none;
        }
        .invalid-feedback {
            color: red;
            font-size: 0.875em;
        }
        .btn {
            background-color: teal;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2 class="text-center">Add Event Team</h2>
    <br>

    <!-- Display success message -->
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Team Name</label>
            <input type="text" name="team_name" class="form-control" value="<?php echo $team_name; ?>">
            <span class="invalid-feedback"><?php echo $team_name_err; ?></span>
        </div>
        
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
            <span class="invalid-feedback"><?php echo $email_err; ?></span>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
            <span class="invalid-feedback"><?php echo $password_err; ?></span>
        </div>

        <div class="form-group">
            <label>Category</label>
            <select name="category" class="form-control">
                <option value="Catering">Catering</option>
                <option value="Decoration">Decoration</option>
                <option value="Venue">Venue</option>
            </select>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control"><?php echo $description; ?></textarea>
        </div>

        <div class="form-group">
            <label>Contact Info</label>
            <input type="text" name="contact_info" class="form-control" value="<?php echo $contact_info; ?>">
        </div>

        <div class="form-group">
            <label>Profile Picture</label>
            <input type="file" name="profile_pic" class="form-control">
            <span class="invalid-feedback"><?php echo $profile_pic_err; ?></span>
        </div>

        <div class="form-group">
            <label>Availability Status</label>
            <input type="checkbox" name="availability_status" value="1" <?php if($availability_status) echo 'checked'; ?>> Available
        </div>

        <div class="form-group">
            <label>Rating</label>
            <input type="number" step="0.1" name="rating" class="form-control" value="<?php echo $rating; ?>">
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="Pending">Pending</option>
                <option value="Approved">Approved</option>
                <option value="Rejected">Rejected</option>
            </select>
        </div>

        <div class="form-group">
            <label>Minimum Price</label>
            <input type="number" name="min_price" class="form-control" step="0.01" value="<?php echo $min_price; ?>">
        </div>

        <div class="form-group">
            <label>Maximum Price</label>
            <input type="number" name="max_price" class="form-control" step="0.01" value="<?php echo $max_price; ?>">
        </div>

        <button type="submit" class="btn">Add Team</button>
    </form>
</div>

</body>
</html>
