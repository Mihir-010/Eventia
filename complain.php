<?php
// Include database connection
include 'includes/db_connect.php';

// Check if the user is logged in
session_start();
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit;
}

// Fetch teams for the dropdown menu
$sql = "SELECT team_id, team_name FROM eventteams";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit a Complaint</title>
    <style>/* General page styling */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
}

h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

/* Form container */
form {
    background-color: #fff;
    max-width: 500px;
    width: 100%;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    margin: 20px;
}

/* Label and input styling */
label {
    display: block;
    font-size: 14px;
    color: #555;
    margin-bottom: 8px;
}

select,
textarea,
button {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
}

select {
    appearance: none;
    background-color: #f9f9f9;
}

textarea {
    resize: vertical;
    min-height: 100px;
}

button {
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #45a049;
}

/* Additional styling for messages */
p {
    text-align: center;
    color: #4CAF50;
    font-size: 16px;
    margin-top: 20px;
}
</style>
</head>
<body>

<h2>Submit a Complaint</h2>

<form action="submit_complaint.php" method="POST">
    <div class="form-group">
        <label>Select Team:</label>
        <select name="team_id" class="form-control" required>
            <option value="">Select a team</option>
            <?php while ($row = $result->fetch_assoc()): ?>
                <option value="<?php echo $row['team_id']; ?>">
                    <?php echo htmlspecialchars($row['team_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Complaint:</label>
        <textarea name="complaint_text" class="form-control" rows="5" required></textarea>
    </div>

    <button type="submit" class="btn">Submit Complaint</button>
</form>

</body>
</html>
