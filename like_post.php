<?php
session_start();
include 'includes/db_connect.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $user_id = $_SESSION['userid']; // Assuming user ID is stored in session

    if ($action === 'like') {
        // Check if the like already exists
        $check_stmt = $conn->prepare("SELECT * FROM post_likes WHERE post_id = ? AND user_id = ?");
        $check_stmt->bind_param("ii", $post_id, $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows == 0) {
            // Insert the like
            $insert_stmt = $conn->prepare("INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)");
            $insert_stmt->bind_param("ii", $post_id, $user_id);
            if ($insert_stmt->execute()) {
                // Increment the likes count in event_team_post
                $update_stmt = $conn->prepare("UPDATE event_team_post SET likes = likes + 1 WHERE post_id = ?");
                $update_stmt->bind_param("i", $post_id);
                $update_stmt->execute();

                $response['success'] = true;
                $response['message'] = 'Liked successfully.';
            } else {
                $response['message'] = 'Error liking the post: ' . $insert_stmt->error;
            }
        } else {
            $response['message'] = 'You have already liked this post.';
        }
    } elseif ($action === 'unlike') {
        // Remove the like
        $delete_stmt = $conn->prepare("DELETE FROM post_likes WHERE post_id = ? AND user_id = ?");
        $delete_stmt->bind_param("ii", $post_id, $user_id);
        if ($delete_stmt->execute()) {
            // Decrement the likes count in event_team_post
            $update_stmt = $conn->prepare("UPDATE event_team_post SET likes = likes - 1 WHERE post_id = ?");
            $update_stmt->bind_param("i", $post_id);
            $update_stmt->execute();

            $response['success'] = true;
            $response['message'] = 'Unliked successfully.';
        } else {
            $response['message'] = 'Error unliking the post: ' . $delete_stmt->error;
        }
    }
}

// Return response as JSON
echo json_encode($response);

// Close statements and connection
$check_stmt->close();
if (isset($insert_stmt)) $insert_stmt->close();
if (isset($delete_stmt)) $delete_stmt->close();
$conn->close();
?>
