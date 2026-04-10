<?php
/**
 * Logout Handler
 * Destroys session and redirects to home
 */

session_start();

// Destroy all session data
$_SESSION = [];
session_destroy();

// Redirect to homepage
header('Location: /?logout=success');
exit;
?>
