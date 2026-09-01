<?php
require_once __DIR__ . '/includes/security.php';
start_secure_session();
require_role('admin', 'admin_login.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_valid_csrf_token();
}

require_once __DIR__ . '/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize the form data
    $courseToRemove = $_POST["cid"];

    try {
        // Check if the course exists in the database
        $checkStmt = $db_connection->prepare("SELECT * FROM courses WHERE cid = :cid");
        $checkStmt->bindParam(':cid', $courseToRemove);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            // Remove course
            $deleteStmt = $db_connection->prepare("DELETE FROM courses WHERE cid = :cid");
            $deleteStmt->bindParam(':cid', $courseToRemove);
            $deleteStmt->execute();

            echo "Course removed successfully!";
        } else {
            echo "Course does not exist!";
        }
    } catch (PDOException $e) {
        echo "Unable to remove course.";
    }
}



$adminId = $_SESSION['admin_id'] ?? null;
$adminUsername = $_SESSION['admin_username'] ?? 'Admin';

date_default_timezone_set('America/New_York');
$currentDateTime = date('F j, Y, g:i a');
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Edit Courses</title>
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
                    <a href="admin_dashboard.php" class="btn btn-dark">Admin</a>
                </div>
                <div class="col text-right">

                    <a href="admin_dashboard.php" class="btn btn-outline-dark">My Account</a>
                    <?php echo logout_form(); ?>
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
                    <a href="add_course.php" class="btn btn-custom-1 btn-block">Add New Course</a>
                </div>
                <div class="col-md-8 main-content loader pr-4">
                    <h5>Remove Courses</h5>
                    <!-- ... Admin Remove Courses Content ... -->
                    <form method="POST" action="edit_courses.php">
                        <?php echo csrf_input(); ?>
                        <label for="cid">Select Course to Remove:</label>
                        <select id="cid" name="cid" class="form-control" required>
                            <?php
                            try {
                                $result = $db_connection->query("SELECT `cid`, `cname`, `instructor` FROM `courses`");

                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                    $courseId = htmlspecialchars($row['cid'], ENT_QUOTES, 'UTF-8');
                                    $courseName = htmlspecialchars($row['cname'], ENT_QUOTES, 'UTF-8');
                                    $instructor = htmlspecialchars($row['instructor'], ENT_QUOTES, 'UTF-8');
                                    echo "<option value=\"$courseId\">$courseId - $courseName ($instructor)</option>";
                                }
                            } catch (PDOException $e) {
                                echo "Unable to load courses.";
                            }
                            ?>
                        </select>

                        <input type="submit" value="Remove Selected Course" class="btn btn-danger mt-3">
                    </form>

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
