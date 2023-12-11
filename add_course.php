<?php
session_start();
include_once 'config.php'; // Include the PDO connection

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cid = $_POST["cid"];
    $course_name = $_POST["cname"];
    $instructor = isset($_POST["instructor"]) ? $_POST["instructor"] : '';
    $day = isset($_POST["day"]) ? $_POST["day"] : '';
    $time = isset($_POST["time"]) ? $_POST["time"] : '';

    try {
        // Check if the course already exists in the database
        $checkStmt = $db_connection->prepare("SELECT * FROM courses WHERE cid = :cid");
        $checkStmt->bindParam(':cid', $cid);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            echo "Course already exists!";
        } else {
            // Insert course
            $insertStmt = $db_connection->prepare("INSERT INTO courses (cid, cname, instructor, day, time) VALUES (:cid, :cname, :instructor, :day, :time)");
            $insertStmt->bindParam(':cid', $cid);
            $insertStmt->bindParam(':cname', $course_name);
            $insertStmt->bindParam(':instructor', $instructor);
            $insertStmt->bindParam(':day', $day);
            $insertStmt->bindParam(':time', $time);
            $insertStmt->execute();

            echo "Course added successfully!";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

$adminId = $_SESSION['admin_id'] ?? null;
$adminUsername = $_SESSION['admin_username'] ?? 'Admin';
date_default_timezone_set('America/New_York');
$currentDateTime = date('F j, Y, g:i a');

?>

<!DOCTYPE html>
<html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Add Student Courses</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/stylesheet.css">
</head>

<body>
    <div class="content-wrap">
        <header>
            <div class="navbar navbar-expand-lg navbar-light padding">
                <div class="col text-left">
                    <a href='student_dashboard.php' class="btn btn-dark">Admin</a>
                </div>
                <div class="col text-right">
                    <a href="admin_dashboard.php" class="btn btn-outline-dark">My Account</a>
                    <a href="logout.php" class="btn btn-dark">Log out</a>
                </div>
            </div>
        </header>
        <!-- Main Content -->
        <main>
            <div class="row padding main-content">
                <div class="col-md-4 sidebar pl-4">
                    <h5>Welcome, <?php echo htmlspecialchars($adminUsername); ?></h5>
                    <p><?php echo $currentDateTime; ?></p>
                    <!-- Admin Sidebar Links -->
                    <a href="admin_view_student_schedules.php" class="btn btn-custom-1 btn-block">View Student Schedules</a>
                    <a href="edit_courses.php" class="btn btn-custom-1 btn-block">Edit Courses</a>
                    <!-- ... other admin links ... -->
                </div>
                <div class="col-md-8 main-content loader pr-4">
                    <h5>Add a new course to the Course Registrar below:</h5>
                    <!-- ... Admin Add Course Content ... -->
                    <div class="form-group row">
                        <form method="POST" action="add_course.php">
                            <label for="cid">Course ID:</label>
                            <input type="text" id="cid" name="cid" required><br><br>

                            <label for="cname">Course Name:</label>
                            <input type="text" id="cname" name="cname" required><br><br>
                            <!-- Existing form fields -->
                            <label for="instructor">Instructor:</label>
                            <input type="text" id="instructor" name="instructor" required><br><br>

                            <label for="day">Day:</label>
                            <input type="text" id="day" name="day" required><br><br>

                            <label for="time">Time:</label>
                            <input type="text" id="time" name="time" required><br><br>

                            <input type="submit" value="Add Course" class="btn btn-primary mt-3">
                        </form>
                    </div>


                </div>

            </div>
        </main>

        <footer>
            <div class="footer row padding pr-4">
                <div class="col text-right">
                    <p>Copyright &copy; 2022 Super Coders</p>
                </div>
            </div>
        </footer>
    </div>
    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</body>

</html>