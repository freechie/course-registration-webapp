<?php
require_once __DIR__ . '/includes/security.php';
start_secure_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

require_valid_csrf_token();
require_once __DIR__ . '/config.php';

$username = is_string($_POST['username'] ?? null) ? trim($_POST['username']) : '';
$password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

$stmt = $db_connection->prepare(
    'SELECT sid, username, password FROM students WHERE username = :username LIMIT 1'
);
$stmt->execute(['username' => $username]);
$student = $stmt->fetch();

if ($student && password_verify($password, $student['password'])) {
    establish_authenticated_session('student', [
        'sid' => (int) $student['sid'],
        'username' => $student['username'],
    ]);
    header('Location: student_dashboard.php');
    exit;
}

header('Location: student_login.php?error=Invalid%20username%20or%20password.');
exit;
