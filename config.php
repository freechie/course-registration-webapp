<?php

// Database settings must be supplied by the runtime environment.
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$database = getenv('DB_NAME');

if ($host === false || $port === false || $user === false || $password === false || $database === false) {
    http_response_code(500);
    exit('Database configuration is incomplete.');
}

$dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";

try {
    $db_connection = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database connection failed.');
}

function fetchCourses($pdo)
{
    $stmt = $pdo->prepare("SELECT cid, cname, day, time FROM courses");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function checkConstraints($pdo, $studentId, $courseId)
{
    $courseStmt = $pdo->prepare("SELECT day, time FROM courses WHERE cid = :cid");
    $courseStmt->bindParam(':cid', $courseId, PDO::PARAM_INT);
    $courseStmt->execute();
    $courseData = $courseStmt->fetch(PDO::FETCH_ASSOC);

    if ($courseData) {
        $stmt = $pdo->prepare("
            SELECT e.* FROM enrolled e
            JOIN courses c ON e.cid = c.cid
            WHERE e.sid = :sid AND c.day = :day AND c.time = :time
        ");
        $stmt->bindParam(':sid', $studentId, PDO::PARAM_INT);
        $stmt->bindParam(':day', $courseData['day'], PDO::PARAM_STR);
        $stmt->bindParam(':time', $courseData['time'], PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return false;
        }
    }

    return true;
}
