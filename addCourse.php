<?php
session_start();
// Turn off error displaying in production environment
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include_once 'config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: student_login.php");
    exit;
}

function addCourse($pdo, $studentId, $courseId)
{
    $stmt = $pdo->prepare("INSERT INTO enrolled (sid, cid) VALUES (:sid, :cid)");
    $stmt->bindParam(':sid', $studentId, PDO::PARAM_INT);
    $stmt->bindParam(':cid', $courseId, PDO::PARAM_INT);

    try {
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        // Handle errors (e.g., schedule conflicts)
        return false;
    }
}

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentId = $_SESSION['sid'];
    $courseId = isset($_POST['cid']) ? $_POST['cid'] : null;

    if ($courseId) {
        // Check for schedule conflicts
        if (checkConstraints($db_connection, $studentId, $courseId)) {
            if (addCourse($db_connection, $studentId, $courseId)) {
                $message = "Course added successfully.";
            } else {
                $message = "Error adding course.";
            }
        } else {
            $message = "Schedule conflict detected.";
        }
    } else {
        $message = "Please select a course.";
    }
}

$courses = fetchCourses($db_connection);
$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
date_default_timezone_set('America/New_york');
$currentDateTime = date('F j, Y, g:i a');


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/stylesheet.css">
</head>

<body>
    <!-- Header -->
    <div class="content-wrap">
        <header>
            <div class="navbar navbar-expand-lg navbar-light padding">
                <div class="col text-left">
                    <a href='student_dashboard.php' class="btn btn-dark">Student</a>
                </div>
                <div class="col text-right">
                    <a href="student_dashboard.php" class="btn btn-link" style="color: #000000;">Home</a>
                    <a href="contact.php" class="btn btn-link" style="color: #000000;">Contact</a>
                    <a href="student_dashboard.php" class="btn btn-outline-dark">My Account</a>
                    <a href="logout.php" class="btn btn-dark">Log out</a>
                </div>
            </div>
        </header>
        <!-- Main Content -->


        <main>
            <div class="row padding main-content">
                <div class="col-md-4 sidebar pl-4">
                    <h5>Welcome, <?php echo htmlspecialchars($userName); ?></h5>
                    <p><?php echo $currentDateTime; ?></p>
                    <!-- Other sidebar content -->
                    <a href="view_current_schedule.php" class="btn btn-custom-1 btn-block">View My Courses</a>
                    <a href="dropCourse.php" class="btn btn-custom-2 btn-block">Drop Course</a>

                </div>
                <div class="col-md-8 main-content loader">
                    <br>
                    <h5>Add Course(s):</h5>
                    <br>
                    <?php if (!empty($message)) echo "<p>$message</p>"; ?>
                    <form method="post">
                        <select name="cid" id="cid" required>
                            <?php foreach ($courses as $course) : ?>
                                <option value="<?php echo htmlspecialchars($course['cid']); ?>">
                                    <?php echo htmlspecialchars($course['cid'] . " - " . $course['cname'] . " - " . $course['day'] . " - " . $course['time']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <br><br>
                        <button type="submit">Add Course</button>
                    </form>
                </div>
            </div>
        </main>
        <footer>
            <div class="footer row padding pr-4">
                <div class="col text-right">
                    <p>Copyright &copy; 2022 Company Name.</p>
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