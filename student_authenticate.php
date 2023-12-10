<?php
// Turn off error displaying in production environment
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();

include_once 'config.php'; // Ensure this file has the correct PDO connection setup

// Validate and sanitize username and password
$username = isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : '';
$password = isset($_POST['password']) ? htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8') : '';

// CSRF token validation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        header("Location: /student_login.php?error=Invalid request.");
        exit();
    }

    // Regenerate CSRF token
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    // Check if user is a student
    $stmt = $db_connection->prepare("SELECT * FROM students WHERE username = :username LIMIT 1");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $student = $stmt->fetch();

        if (password_verify($password, $student['password'])) {
            // Regenerate session ID upon successful login
            session_regenerate_id();

            $_SESSION['loggedin'] = true;
            $_SESSION['sid'] = $student['sid'];
            $_SESSION['username'] = $student['username'];

            header("Location: student_dashboard.php");
            exit();
        } else {
            header("Location: /student_login.php?error=Invalid username or password.");
            exit();
        }
    } else {
        header("Location: /student_login.php?error=Invalid username or password.");
        exit();
    }
}
?>
