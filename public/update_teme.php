<?php
session_start();
if (isset($_POST['theme'])) {
    $_SESSION['theme'] = $_POST['theme'];
    error_log("Theme set to: {$_SESSION['theme']} for user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'none') . " at " . date('Y-m-d H:i:s'));
    header('Location: ' . $_SERVER['HTTP_REFERER']); // Redirect back to the same page
    exit;
}
?>