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
include 'header.php';

// Fetch all users
$query = "SELECT user_id, username, email, phone, created_at FROM users";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: teal;
            color: white;
        }
        .edit-btn, .delete-btn, .add-btn {
            padding: 5px 10px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
        }
        .edit-btn {
            background-color: blue;
        }
        .delete-btn {
            background-color: red;
        }
        .add-btn {
            background-color: green;
        }
    </style>
</head>
<body>
<br><br><br>
<h2><center>User Management</center></h2>
<br><br><br>

<!-- Display success or error message -->
<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_GET['msg']); ?>
    </div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>

<table>
    <tr>
        <th>User ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Created At</th>
        <th>Actions</th>
    </tr>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['user_id']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td><?php echo $row['created_at']; ?></td>
                <td>
                    <a href="delete-user.php?user_id=<?php echo $row['user_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user and their reviews?');">Delete</a>

                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="6">No users found.</td>
        </tr>
    <?php endif; ?>
</table>

<br><br>
<center><a href="add_user.php" class="add-btn">Add User</a></center>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
<?php if (isset($_GET['message'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_GET['message']); ?>
    </div>
<?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>
