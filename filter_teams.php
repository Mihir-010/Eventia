<?php
// Start session and include database connection
session_start();
include 'includes/db_connect.php';
include 'header.php';

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Initialize filter variables
$category = '';
$searchTerm = '';
$minRating = 0;
$minCost = 0;
$maxCost = 0;

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['filter'])) {
    // Retrieve filter values from the URL
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    $minRating = isset($_GET['min_rating']) ? (float)$_GET['min_rating'] : 0;
    $minCost = isset($_GET['min_cost']) ? (float)$_GET['min_cost'] : 0;
    $maxCost = isset($_GET['max_cost']) ? (float)$_GET['max_cost'] : 0;

    // Build the SQL query for filtering
    $sql = "SELECT * FROM eventteams WHERE status = 'Approved' AND availability_status = 1";
    $conditions = [];
    $params = [];
    $types = '';

    // Add conditions based on user inputs
    if (!empty($category)) {
        $conditions[] = "category = ?";
        $params[] = $category;
        $types .= 's';
    }
    if (!empty($searchTerm)) {
        $conditions[] = "(team_name LIKE ? OR description LIKE ?)";
        $params[] = '%' . $searchTerm . '%';
        $params[] = '%' . $searchTerm . '%';
        $types .= 'ss';
    }
    if ($minRating > 0) {
        $conditions[] = "rating >= ?";
        $params[] = $minRating;
        $types .= 'd';
    }
    if ($minCost > 0) {
        $conditions[] = "min_price >= ?";
        $params[] = $minCost;
        $types .= 'd';
    }
    if ($maxCost > 0) {
        $conditions[] = "max_price <= ?";
        $params[] = $maxCost;
        $types .= 'd';
    }

    // Append conditions to the SQL query
    if (count($conditions) > 0) {
        $sql .= " AND " . implode(' AND ', $conditions);
    }

    // Prepare statement
    $stmt = $conn->prepare($sql);

    // Dynamically bind the parameters if there are any
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Eventia - Filter Event Teams</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
           .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-10px);
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .card-body {
            padding: 20px;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .card-text {
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .rating {
            color: #FFD700;
        }
        .filter-form {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap; /* Allow wrapping on smaller screens */
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .filter-form select,
    .filter-form input[type="text"],
    .filter-form input[type="number"] {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        min-width: 150px; /* Set a minimum width */
        transition: border-color 0.3s;
    }

    .filter-button {
        padding: 8px 15px;
        border: none;
        border-radius: 5px;
        background-color: #007bff;
        color: white;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .filter-button:hover {
        background-color: #0056b3;
    }


    </style>
</head>
<body>
<section class="food_section layout_padding">
    <div class="container">
        <div class="heading_container heading_center">
            <h2>Filter Event Teams</h2><br><br>
        </div>

        <form method="GET" action="filter_teams.php" class="filter-form">
    <div class="form-group">
        <label for="category">Category:</label>
        <select name="category" id="category">
            <option value="">All Categories</option>
            <option value="venue" <?php if ($category == 'venue') echo 'selected'; ?>>Venue</option>
            <option value="catering" <?php if ($category == 'catering') echo 'selected'; ?>>Catering</option>
            <option value="decoration" <?php if ($category == 'decoration') echo 'selected'; ?>>Decoration</option>
        </select>
    </div>

    <div class="form-group">
        <label for="search">Search:</label>
        <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search by name or description">
    </div>

    <div class="form-group">
        <label for="min_rating">Minimum Rating:</label>
        <input type="number" name="min_rating" id="min_rating" value="<?php echo $minRating; ?>" min="0" max="5" step="0.1" placeholder="0 - 5">
    </div>

    <div class="form-group">
        <label for="min_cost">Min Cost:</label>
        <input type="number" name="min_cost" id="min_cost" value="<?php echo isset($_GET['min_cost']) ? $_GET['min_cost'] : ''; ?>" placeholder="Minimum Cost">
    </div>

    <div class="form-group">
        <label for="max_cost">Max Cost:</label>
        <input type="number" name="max_cost" id="max_cost" value="<?php echo isset($_GET['max_cost']) ? $_GET['max_cost'] : ''; ?>" placeholder="Maximum Cost">
    </div>

    <button type="submit" name="filter" class="filter-button">Filter</button>
</form>


        <!-- Display Event Teams -->
        <div class="row">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="col-md-4">';
                    echo '<a href="team_details.php?team_id=' . $row['team_id'] . '" class="card-link">';
                    echo '<div class="card">';
                    echo '<img src="uploads/' . $row['profile_pic'] . '" class="card-img-top" alt="' . htmlspecialchars($row['team_name']) . '">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($row['team_name']) . '</h5>';
                    echo '<p class="card-text">' . htmlspecialchars($row['description']) . '</p>';
                    echo '<p class="card-text"><strong>Contact:</strong> ' . htmlspecialchars($row['contact_info']) . '</p>';
                    echo '<p class="card-text"><strong>Rating:</strong></p><div class="rating">';
                    
                    $rating = $row['rating'];
                    for ($i = 1; $i <= 5; $i++) {
                        echo $i <= $rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                    }
                    
                    echo '</div></div></div></a></div>';
                }
            } else {
                echo '<p>No results found for the applied filters.</p>';
            }
            ?>
        </div>
    </div>
</section>
</body>
</html>
