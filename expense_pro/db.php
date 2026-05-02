<?php
session_start();
$conn = new mysqli("localhost", "root", "", "expense_tracker");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Security Helper
function sanitize($conn, $data) {
    return mysqli_real_escape_string($conn, htmlspecialchars($data));
}
?>