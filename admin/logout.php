<?php
require_once '../config/database.php';

// Destroy session and redirect
session_destroy();
header("Location: " . BASE_URL . "admin/login.php");
exit();
?>