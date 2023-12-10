<?php
// Turn off error displaying in production environment
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();

include_once 'config.php'; // Ensure this file has the correct PDO connection setup

// Validate and sanitize username and password
$admin_username = isset($_POST['admin_username']) ? htmlspecialchars($_POST['admin_username'], ENT_QUOTES, 'UTF-8') : '';
$admin_password = isset($_POST['admin_password']) ? htmlspecialchars($_POST['admin_password'], ENT_QUOTES, 'UTF-8') : '';

// CSRF token validation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        header("Location: /admin_login.php?error=Invalid request.");
        exit();
    }

    // Regenerate CSRF token
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    // Check if the user is an admin
    $adminStmt = $db_connection->prepare("SELECT * FROM admins WHERE admin_username = :admin_username LIMIT 1");
    $adminStmt->bindParam(':admin_username', $admin_username, PDO::PARAM_STR);
    $adminStmt->execute();

    if ($adminStmt->rowCount() == 1) {
        $admin = $adminStmt->fetch();

        if (password_verify($admin_password, $admin['admin_password'])) {
            // Regenerate session ID upon successful login
            session_regenerate_id();

            $_SESSION['loggedin'] = true;
            $_SESSION['admin_username'] = $admin['admin_username'];
            $_SESSION['is_admin'] = true; // Indicate that the user is an admin

            header("Location: admin_dashboard.php");
            exit();
        } else {
            header("Location: /admin_login.php?error=Invalid username or password.");
            exit();
        }
    } else {
        header("Location: /admin_login.php?error=Invalid username or password.");
        exit();
    }
}
?>
