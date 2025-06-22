<?php
require_once '../config/config.php';

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
redirect(SITE_URL . '/pages/login.php');
?>