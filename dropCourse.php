<?php
session_start();
// Turn off error displaying in production environment
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include_once 'config.php';

// Redirect if not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: student_login.php");
    exit;
}

$studentId = $_SESSION['sid'] ?? null;
$userName = $_SESSION['username'] ?? 'Guest';

// Set the default timezone if necessary
date_default_timezone_set('America/New_york'); 
$currentDateTime = date('F j, Y, g:i a');

// Fetch enrolled courses for the logged-in student
function fetchEnrolledCourses($pdo, $studentId)
{
    $stmt = $pdo->prepare("SELECT c.cid, c.cname FROM enrolled e JOIN courses c ON e.cid = c.cid WHERE e.sid = :sid");
    $stmt->bindParam(':sid', $studentId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Drop a course
function dropCourse($pdo, $studentId, $courseId)
{
    $stmt = $pdo->prepare("DELETE FROM enrolled WHERE sid = :sid AND cid = :cid");
    $stmt->bindParam(':sid', $studentId, PDO::PARAM_INT);
    $stmt->bindParam(':cid', $courseId, PDO::PARAM_INT);

    try {
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

$enrolledCourses = fetchEnrolledCourses($db_connection, $studentId);

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $courseId = $_POST['cid'] ?? null;

    if ($courseId && dropCourse($db_connection, $studentId, $courseId)) {
        $message = "Course dropped successfully.";
        $enrolledCourses = fetchEnrolledCourses($db_connection, $studentId); // Refresh the list
    } else {
        $message = "Error dropping course.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drop Course</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/stylesheet.css">
</head>

<body>
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
        <main>
            <div class="row padding main-content">
                <!-- Sidebar -->
                <div class="col-md-4 sidebar pl-4">
                    <!-- The date and time can be dynamically generated using PHP -->
                    <h5>Welcome, <?php echo htmlspecialchars($userName); ?></h5>
                    <p><?php echo $currentDateTime; ?></p>
                    <a href="view_current_schedule.php" class="btn btn-custom-1 btn-block">View My Courses</a>
                    <a href="addCourse.php" class="btn btn-custom-2 btn-block">Add Course</a>
                </div>
                <div class="col-md-8 pr-4">
                    <h5>Drop a Course:</h5>
                    <?php if ($message) : ?>
                        <p><?php echo $message; ?></p>
                    <?php endif; ?>
                    <form method="post">
                        <div class="form-group">
                            <label for="cid">Select Course to Drop:</label>
                            <select name="cid" id="cid" class="form-control" required>
                                <?php foreach ($enrolledCourses as $course) : ?>
                                    <option value="<?php echo htmlspecialchars($course['cid']); ?>">
                                        <?php echo htmlspecialchars($course['cname']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-danger">Drop Course</button>
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
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>