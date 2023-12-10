<?php

// DB credentials
$host = "localhost";
$user = "root";
$password = "";
$database = "finalprojdatabase";

// Set DSN for the PDO connection
$dsn = "mysql:host=$host;dbname=$database";

try {
    // Create a PDO connection
    $db_connection = new PDO($dsn, $user, $password);
    // Set the PDO error mode to exception
    $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // echo "Connected successfully";
} catch (PDOException $e) {
    // Handle connection error
    die("Connection failed: " . $e->getMessage());
}


// Fetch courses from the database
function fetchCourses($pdo)
{
    $stmt = $pdo->prepare("SELECT cid, cname, day, time FROM courses");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function checkConstraints($pdo, $studentId, $courseId)
{
    // First, get the day and time of the course the student is trying to add
    $courseStmt = $pdo->prepare("SELECT day, time FROM courses WHERE cid = :cid");
    $courseStmt->bindParam(':cid', $courseId, PDO::PARAM_INT);
    $courseStmt->execute();
    $courseData = $courseStmt->fetch(PDO::FETCH_ASSOC);

    if ($courseData) {
        // Now check if the student is already enrolled in a course at this day and time
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
            // Conflict found
            return false;
        }
    }

    return true;
}
