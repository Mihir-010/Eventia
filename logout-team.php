<?php
// Start the session
session_start();

// Unset specific session variables related to event teams
unset($_SESSION['teamid']); // Adjust this based on the actual session variables used

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: login_event_team.php");
exit();
?>
