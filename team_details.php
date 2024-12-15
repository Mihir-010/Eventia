<?php
// Start session and include database connection
session_start();
include 'includes/db_connect.php';
include 'header.php';

// Fetch the team_id from the URL
$team_id = isset($_GET['team_id']) ? $_GET['team_id'] : 0;

// Prepare SQL query to fetch event team details based on the team_id
$stmt = $conn->prepare("SELECT * FROM eventteams WHERE team_id = ?");
$stmt->bind_param("i", $team_id);
$stmt->execute();
$team_result = $stmt->get_result();

// Fetch the team's posts along with likes and user like status
$post_stmt = $conn->prepare("
    SELECT p.*, 
           (SELECT COUNT(*) FROM post_likes WHERE post_id = p.post_id) AS likes,
           (SELECT COUNT(*) FROM post_likes WHERE post_id = p.post_id AND user_id = ?) AS user_liked
    FROM event_team_post p WHERE p.team_id = ?
");
$post_stmt->bind_param("ii", $_SESSION['userid'], $team_id);
$post_stmt->execute();
$post_result = $post_stmt->get_result();

// Check if the team exists
if ($team_result->num_rows > 0) {
    $team = $team_result->fetch_assoc();
    
    // Define $isVenueTeam, $isCateringTeam, and $isDecorationTeam based on the team's category
    $isVenueTeam = $team['category'] === 'Venue';
    $isCateringTeam = $team['category'] === 'Catering';
    $isDecorationTeam = $team['category'] === 'Decoration';
    ?>

    <div class="container">
        <div class="team-details">
            <h2><center><?php echo htmlspecialchars($team['team_name']); ?></center></h2>
            <p><strong>Category:</strong> <?php echo ucfirst(htmlspecialchars($team['category'])); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($team['description']); ?></p>
            <p><strong>Contact:</strong> <?php echo htmlspecialchars($team['contact_info']); ?></p>
            <p><strong>Rating:</strong> <span id="team-likes-count"><?php echo htmlspecialchars($team['rating']); ?></span> / 5</p>
            <!-- Reviews Slider -->
<!-- Reviews Slider -->
<div class="reviews-section">
    <h3>Reviews</h3>
    <div id="reviewsCarousel" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <?php
            // Fetch reviews for the team with user names
            $review_stmt = $conn->prepare("
                SELECT u.username AS user_name, r.comment AS review_text, r.created_at, r.rating
                FROM reviews r 
                JOIN users u ON r.user_id = u.user_id 
                WHERE r.team_id = ? 
                ORDER BY r.created_at DESC
            ");
            $review_stmt->bind_param("i", $team_id);
            $review_stmt->execute();
            $review_result = $review_stmt->get_result();

            // Display Reviews in Slider
            if ($review_result->num_rows > 0) {
                $first = true; // To handle the active class for the first item
                while ($review = $review_result->fetch_assoc()) {
                    $user_name = htmlspecialchars($review['user_name']);
                    $review_text = htmlspecialchars($review['review_text']);
                    $created_at = htmlspecialchars($review['created_at']);
                    $rating = (int)$review['rating']; // Assuming rating is stored as an integer

                    echo '<div class="carousel-item ' . ($first ? 'active' : '') . '">';
                    echo '<div class="review">';
                    echo '<p><strong>' . $user_name . ':</strong> ' . $review_text . '</p>';
                    echo '<p><em>Posted on ' . $created_at . '</em></p>';
                    
                    // Display Rating
                    echo '<div class="rating">';
                    for ($i = 1; $i <= 5; $i++) {
                        echo '<span class="star" style="color:' . ($i <= $rating ? '#FFD700' : 'lightgray') . ';">★</span>';
                    }
                    echo '</div>';
                    
                    echo '</div>'; // End of review
                    echo '</div>'; // End of carousel-item
                    $first = false; // Subsequent items should not be active
                }
            } else {
                echo '<p>No reviews available for this team.</p>';
            }

            // Close the review statement
            $review_stmt->close();
            ?>
        </div>
        <a class="carousel-control-prev" href="#reviewsCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#reviewsCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</div>




            <h3>Recent Posts</h3>
            <?php
            // Display posts if any exist
            if ($post_result->num_rows > 0) {
                while ($post = $post_result->fetch_assoc()) {
                    $liked = $post['user_liked'] > 0;
                    $like_count = $post['likes'];
                    echo '<div class="blog-post">';
                    echo '  <h4>' . htmlspecialchars($post['title']) . '</h4>';
                    echo '  <p><em>Posted on ' . htmlspecialchars($post['created_at']) . '</em></p>';
                    echo '  <p>' . htmlspecialchars($post['description']) . '</p>';
                    echo '  <img src="uploads/' . htmlspecialchars($post['image']) . '" alt="' . htmlspecialchars($post['title']) . '" class="img-fluid">';
                    echo '  <p>';
                    echo '      <strong>Likes:</strong> <span id="likes-count-' . htmlspecialchars($post['post_id']) . '">' . htmlspecialchars($like_count) . '</span> ';
                    echo '      <button class="like-button" data-post-id="' . htmlspecialchars($post['post_id']) . '">';
                    echo $liked ? '<span class="heart liked">❤️</span>' : '<span class="heart">🤍</span>';
                    echo '      </button>';
                    echo '  </p>';
                    echo '  <hr>';
                    echo '</div>';
                }
            } else {
                echo '<p>No posts available for this team.</p>';
            }
            ?>

<form action="book_team.php" method="post">
    <input type="hidden" name="team_id" value="<?php echo htmlspecialchars($team['team_id']); ?>">

    <?php if ($isVenueTeam): ?>
        <div class="form-group">
            <label for="num_people">Number of People Attending:</label>
            <input type="number" class="form-control" id="num_people" name="num_people" required>
        </div>
        <div class="form-group">
            <label for="event_date">Event Date (Select Date from Calendar):</label>
            <input type="text" class="form-control" id="event_date" name="event_date" required>
        </div>
        <div id="calendar"></div>
    <?php elseif ($isCateringTeam): ?>
        <div class="form-group">
            <label for="num_people">Number of People Attending:</label>
            <input type="number" class="form-control" id="num_people" name="num_people" required>
        </div>
        <div class="form-group">
            <label for="event_date">Event Date:</label>
            <input type="date" class="form-control" id="event_date" name="event_date" min="<?php echo date('Y-m-d'); ?>" required>
        </div>
    <?php elseif ($isDecorationTeam): ?>
        <div class="form-group">
            <label for="event_date">Event Date:</label>
            <input type="date" class="form-control" id="event_date" name="event_date" min="<?php echo date('Y-m-d'); ?>" required>
        </div>
    <?php endif; ?>

    <button type="submit" class="btn btn-primary">Book Now</button>
</form>

            <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                $(document).ready(function () {
                    // Check if the venue team is active
                    if ($('#event_date').length) {
                        var calendarEl = document.getElementById('calendar');

                        var calendar = new FullCalendar.Calendar(calendarEl, {
                            initialView: 'dayGridMonth',
                            headerToolbar: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'dayGridMonth,timeGridWeek,timeGridDay'
                            },
                            events: [
                                // Example events with colors (replace with dynamic data)
                                {
                                    title: 'Venue Booking 1',
                                    start: '2024-10-25',
                                    end: '2024-10-27',
                                    color: '#ff6347', // Tomato color for booked events
                                    textColor: '#fff' // White text for better readability
                                },
                                {
                                    title: 'Venue Booking 2',
                                    start: '2024-10-30',
                                    end: '2024-11-02',
                                    color: '#4682b4', // Steel blue for another booked event
                                    textColor: '#fff'
                                },
                                {
                                    title: 'Available Slot',
                                    start: '2024-11-05',
                                    end: '2024-11-06',
                                    color: '#32cd32', // Lime green for available slots
                                    textColor: '#000' // Black text for contrast
                                }
                            ],
                            dateClick: function (info) {
                                $('#event_date').val(info.dateStr); // Populate the date in the input
                            }
                        });

                        calendar.render();
                    }
                });
            </script>
        </div>
    </div>

    <?php
} else {
    echo '<p>Team not found.</p>';
}

// Close the statements and connection
$stmt->close();
$post_stmt->close();
$conn->close();
?>

<!-- CSS Styling -->
<style>
.blog-post {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    background-color: #f9f9f9;
}

.blog-post h4 {
    font-size: 1.5rem;
}

.blog-post img {
    width: 100%;
    height: auto;
    margin-bottom: 10px;
}

.like-button {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.5rem;
}

.liked {
    color: red; /* Change heart color when liked */
}

.heart {
    font-size: 24px; /* Adjust size as needed */
    cursor: pointer;
}

#calendar {
    margin-top: 20px;
}

.fc-event-title {
    font-weight: bold;
}
.reviews-section {
    margin: 20px 0;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
}

.reviews-section h3 {
    margin-bottom: 15px;
    color: #333;
}

.review {
    margin-bottom: 10px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 3px;
    background-color: #fff;
}

.review p {
    margin: 5px 0;
}

.review strong {
    color: #007bff; /* Bootstrap primary color */
}

.review em {
    font-size: 0.9em;
    color: #666;
}
.rating .star {
    font-size: 20px;  /* Adjust size as needed */
    color: #FFD700;   /* Color for filled stars (gold/yellow) */
}


</style>

<!-- Include Font Awesome and jQuery -->
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<!-- Add Bootstrap CSS in the <head> section -->
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<!-- Add Bootstrap JS and jQuery before the closing </body> tag -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</head>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Like/Unlike Script -->
<script>
    $(document).ready(function () {
        $('.like-button').on('click', function () {
            var postId = $(this).data('post-id');
            var likesCountElement = $('#likes-count-' + postId);
            var currentLikes = parseInt(likesCountElement.text());
            var liked = $(this).find('.heart').hasClass('liked');
            var action = liked ? 'unlike' : 'like';

            $.ajax({
                url: 'like_post.php',
                method: 'POST',
                data: { post_id: postId, action: action },
                success: function (response) {
                    var result = JSON.parse(response);
                    if (result.success) {
                        if (action === 'like') {
                            likesCountElement.text(currentLikes + 1);
                            $(this).find('.heart').addClass('liked').html('❤️'); // Red heart
                        } else {
                            likesCountElement.text(currentLikes - 1);
                            $(this).find('.heart').removeClass('liked').html('🤍'); // White heart
                        }
                    } else {
                        alert(result.message);
                    }
                }.bind(this),
                error: function () {
                    alert('Error processing request. Please try again.');
                }
            });
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    // Store confirmed and pending dates
    var bookedDates = [];

    // Get today's date and calculate tomorrow's date
    var today = new Date();
    var tomorrow = new Date();
    tomorrow.setDate(today.getDate() + 1); // Set to tomorrow

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: true,
        // Set the minimum selectable date to tomorrow
        validRange: {
            start: tomorrow // Users can only select dates from tomorrow onward
        },
        events: 'fetch-venue-bookings.php?team_id=<?php echo $team_id; ?>', // Load events from this URL
        eventDidMount: function(info) {
            // Store the event status and date in an array for later checks
            bookedDates.push({
                date: info.event.startStr, // The event date in string format (yyyy-mm-dd)
                status: info.event.title // The status of the booking
            });
        },
        dateClick: function(info) {
            // Check if the clicked date is already booked
            let isBooked = false;
            let bookingStatus = '';

            // Loop through booked dates to check if the clicked date is already booked
            bookedDates.forEach(function(booking) {
                if (booking.date === info.dateStr) {
                    isBooked = true;
                    bookingStatus = booking.status; // Get the status (Confirmed or Pending)
                }
            });

            // If the date is confirmed or pending, show an alert and prevent selection
            if (isBooked) {
                if (bookingStatus === 'Confirmed') {
                    alert("This date is already confirmed for an event. Please choose another date.");
                } else if (bookingStatus === 'Pending') {
                    alert("This date is pending approval. Please choose another date.");
                }
            } else {
                // If the date is not booked, allow the user to select it
                console.log('Date clicked: ' + info.dateStr);
                document.getElementById('event_date').value = info.dateStr; // Update event_date input with the selected date
            }
        }
    });

    calendar.render();
});
</script>


