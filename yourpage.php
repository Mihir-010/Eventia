<?php
// Start session and include database connection
session_start();
include 'includes/db_connect.php';
include 'header.php';

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    // If the session userid is not set, redirect to the login page
    header("Location: login.php");
    exit(); // Stop further script execution after redirect
}

// Define default values
$category = isset($_GET['category']) ? $_GET['category'] : 'venue';
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$minRating = isset($_GET['min_rating']) ? $_GET['min_rating'] : 0;

// Prepare SQL query to fetch approved and available event teams based on selected filters
$sql = "SELECT * FROM eventteams WHERE category = ? AND status = 'Approved' AND availability_status = 1 
        AND (team_name LIKE ? OR description LIKE ?) 
        AND rating >= ?"; 

$stmt = $conn->prepare($sql);
$searchWildcard = '%' . $searchTerm . '%';
$stmt->bind_param("sssi", $category, $searchWildcard, $searchWildcard, $minRating);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Eventia - Event Teams</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- For star icons -->
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

        .filters_menu li {
            display: inline-block;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            margin-right: 5px;
            background-color: #f1f1f1;
        }

        .filters_menu li.active {
            background-color: #333;
            color: #fff;
        }

        .filters_menu {
            margin-bottom: 30px;
            text-align: center;
        }

        .search-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
        }

        .search-section form {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .search-input,
        .search-select,
        .search-button {
            margin: 5px;
            padding: 10px;
            font-size: 1rem;
            border-radius: 5px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: border-color 0.3s;
        }

        .search-input:focus,
        .search-select:focus {
            border-color: #333;
            outline: none;
        }

        .search-button {
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-button:hover {
            background-color: #555;
        }

        /* Specific styles for the input and select elements */
        .search-input {
            width: 200px; /* Set a specific width for the search input */
        }

        .search-select {
            width: 120px; /* Set a specific width for select elements */
        }

        /* Style for the search/filter buttons */
        .filter-buttons {
            text-align: right;
            margin-bottom: 20px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .search-section form {
                flex-direction: column; /* Stack elements vertically on smaller screens */
                align-items: stretch; /* Align items to stretch full width */
            }

            .search-input,
            .search-select,
            .search-button {
                width: 100%; /* Full width for inputs and buttons on small screens */
            }
        }
        .search-button {
    background-color: #333; /* Dark background */
    color: white; /* White text */
    border: none; /* No border */
    padding: 10px 15px; /* Padding */
    border-radius: 5px; /* Rounded corners */
    cursor: pointer; /* Pointer cursor on hover */
    transition: background-color 0.3s; /* Smooth background transition */
}

.search-button:hover {
    background-color: #555; /* Lighter background on hover */
}
        
    </style>
</head>

<body>

    <section class="food_section layout_padding">
        <div class="container">
            <div class="heading_container heading_center">
                <h2>Our Services - <?php echo ucfirst($category); ?></h2>
            </div>

            <ul class="filters_menu">
                <li class="<?php if ($category == 'venue') echo 'active'; ?>" data-filter="venue">Venue</li>
                <li class="<?php if ($category == 'catering') echo 'active'; ?>" data-filter="catering">Catering</li>
                <li class="<?php if ($category == 'decoration') echo 'active'; ?>" data-filter="decoration">Decoration</li>
            </ul>

            <!-- Search and Filter Section -->
            <p align="right">
                <button class="search-button" onclick="window.location.href='filter_teams.php'">Search and Filter</button>
            </p>

                
            <div class="row">
            <?php
// Prepare SQL query to fetch approved and available event teams based on selected category
$stmt = $conn->prepare("SELECT * FROM eventteams WHERE category = ? AND status = 'Approved' AND availability_status = 1");
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();

// Display the event teams in cards
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="col-md-4">';
        
        // Add a link to team_details.php with team_id
        echo '  <a href="team_details.php?team_id=' . $row['team_id'] . '" class="card-link">';
        
        echo '    <div class="card">';
        echo '      <img src="uploads/' . $row['profile_pic'] . '" class="card-img-top" alt="' . htmlspecialchars($row['team_name']) . '">';
        echo '      <div class="card-body">';
        echo '        <h5 class="card-title">' . htmlspecialchars($row['team_name']) . '</h5>';
        echo '        <p class="card-text">' . htmlspecialchars($row['description']) . '</p>';
        echo '        <p class="card-text"><strong>Contact:</strong> ' . htmlspecialchars($row['contact_info']) . '</p>';
        
        // Fetch the review count for the current team
        $teamId = $row['team_id'];
        $review_stmt = $conn->prepare("SELECT COUNT(*) AS review_count FROM reviews WHERE team_id = ?");
        $review_stmt->bind_param("i", $teamId);
        $review_stmt->execute();
        $review_result = $review_stmt->get_result();
        $review_data = $review_result->fetch_assoc();
        $reviewCount = $review_data['review_count'] ?? 0; // Default to 0 if no reviews

        // Rating section with text and stars
        echo '        <p class="card-text"><strong>Rating:</strong></p>';
        echo '        <div class="rating">';

        // Display star ratings
        $rating = $row['rating'];
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                echo '<i class="fas fa-star" style="color: yellow;"></i>'; // Yellow star
            } else {
                echo '<i class="far fa-star"></i>'; // Empty star
            }
        }
        echo ' (' . $reviewCount . ' reviews)'; // Display the review count
        echo '        </div>'; // Close rating div
        echo '      </div>'; // Close card-body div
        echo '    </div>'; // Close card div
        echo '  </a>'; // Close card-link
        echo '</div>'; // Close col-md-4 div

        // Close the review statement
        $review_stmt->close();
    }
} else {
    echo '<p>No event teams found for the selected category.</p>';
}
?>

            </div>
        </div>
    </section>

    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://unpkg.com/isotope-layout@3.0.4/dist/isotope.pkgd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/js/jquery.nice-select.min.js"></script>
    <script src="js/custom.js"></script>

    <script>
        $(document).ready(function () {
            $('.filters_menu li').on('click', function () {
                $('.filters_menu li').removeClass('active');
                $(this).addClass('active');

                const category = $(this).data('filter'); // Get the selected category
                window.location.href = `yourpage.php?category=${category}`; // Redirect with selected category
            });
        });
    </script>
</body>

</html>

<?php
// Close the database connection
$stmt->close();
$conn->close();
?>
