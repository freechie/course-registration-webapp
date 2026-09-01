<?php

require_once __DIR__ . '/../config.php';

$password = password_hash('Admin Test Password', PASSWORD_DEFAULT);
$stmt = $db_connection->prepare(
    'UPDATE admins SET admin_password = :password WHERE admin_username = :username'
);
$stmt->execute([
    'password' => $password,
    'username' => 'admin_example',
]);

if ($stmt->rowCount() !== 1) {
    fwrite(STDERR, "Failed to seed the test administrator." . PHP_EOL);
    exit(1);
}
