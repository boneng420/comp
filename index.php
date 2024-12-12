<?php
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_type'] == 'instructor') {
        header("Location: instructor/dashboard.php");
    } else if ($_SESSION['user_type'] == 'student') {
        header("Location: student/dashboard.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Learning Platform</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Learning Platform</h1>
        <div class="auth-options">
            <a href="login.php" class="btn">Login</a>
            <a href="register.php" class="btn">Register</a>
        </div>
    </div>
</body>
</html>