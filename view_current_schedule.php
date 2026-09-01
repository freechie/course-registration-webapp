<?php
require_once __DIR__ . '/includes/security.php';
start_secure_session();
require_role('student', 'student_login.php');
require_once __DIR__ . '/config.php';

$studentId = $_SESSION['sid'];
$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// Get current date and time
// Set the default timezone if necessary
date_default_timezone_set('America/New_York'); // Example: 'America/New_York'
$currentDateTime = date('F j, Y, g:i a'); // Adjust the format as needed

function getStudentSchedule($pdo, $studentId)
{
    $stmt = $pdo->prepare("SELECT c.cid, c.cname, c.instructor, c.day, c.time 
                           FROM enrolled e 
                           JOIN courses c ON e.cid = c.cid 
                           WHERE e.sid = :sid");
    $stmt->bindParam(':sid', $studentId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$schedule = getStudentSchedule($db_connection, $studentId);


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Current Schedule</title>
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
                    <a href="student_dashboard.php" class="btn btn-outline-dark">My Account</a>
                    <?php echo logout_form(); ?>
                </div>
            </div>
        </header>
        <!-- Main Content -->
        <main>
            <div class="row padding main-content">
                <!-- Sidebar -->
                <div class="col-md-4 sidebar pl-4">
                    <h5>Welcome, <?php echo htmlspecialchars($userName); ?></h5>
                    <p><?php echo $currentDateTime; ?></p>
                    <a href="addCourse.php" class="btn btn-custom-1 btn-block">Add Course</a>
                    <a href="dropCourse.php" class="btn btn-custom-2 btn-block">Drop Course</a>
                </div>
                <!-- Content next to left sidebar -->
                <div class="col-md-8 main-content loader pr-4">
                    <br>
                    <h5>Current Schedule:</h5>
                    <br>
                    <div class="table-scroll">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Course ID</th>
                                <th>Course Name</th>
                                <th>Instructor</th>
                                <th>Day</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedule as $course) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($course['cid']); ?></td>
                                    <td><?php echo htmlspecialchars($course['cname']); ?></td>
                                    <td><?php echo htmlspecialchars($course['instructor']); ?></td>
                                    <td><?php echo htmlspecialchars($course['day']); ?></td>
                                    <td><?php echo htmlspecialchars($course['time']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                </div>

            </div>
        </main>
    </div>
    <!-- Footer -->
    <footer>
        <div class="footer row padding pr-4">
            <div class="col text-right">
                <p>Copyright &copy; 2022 Super Coders.</p>
            </div>
        </div>

    </footer>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>
