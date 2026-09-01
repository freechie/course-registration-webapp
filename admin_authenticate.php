<?php
require_once __DIR__ . '/includes/security.php';
start_secure_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

require_valid_csrf_token();
require_once __DIR__ . '/config.php';

$adminUsername = is_string($_POST['admin_username'] ?? null) ? trim($_POST['admin_username']) : '';
$adminPassword = is_string($_POST['admin_password'] ?? null) ? $_POST['admin_password'] : '';

$stmt = $db_connection->prepare(
    'SELECT admin_username, admin_password FROM admins WHERE admin_username = :admin_username LIMIT 1'
);
$stmt->execute(['admin_username' => $adminUsername]);
$admin = $stmt->fetch();

if ($admin && password_verify($adminPassword, $admin['admin_password'])) {
    establish_authenticated_session('admin', [
        'admin_username' => $admin['admin_username'],
    ]);
    header('Location: admin_dashboard.php');
    exit;
}

header('Location: admin_login.php?error=Invalid%20username%20or%20password.');
exit;
