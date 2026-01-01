<?php
session_start();
if (!isset($_SESSION['role'])) {
    die("Access Denied");
}
$requestedType = $_GET['type'] ?? 'user';
if ($requestedType === 'admin' && $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die("Forbidden: You are not allowed to access this report.");
}
if ($requestedType === 'admin') {
    echo "Admin Report";
} else {
    echo "User Report";
}
?>
