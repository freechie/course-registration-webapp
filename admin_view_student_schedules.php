<?php
session_start();
include_once 'config.php'; // Include the database configuration file

// Function to fetch the total number of pages
function getTotalPages($db, $recordsPerPage)
{
    $stmt = $db->query("SELECT COUNT(sid) FROM students");
    $totalRecords = $stmt->fetchColumn();
    return ceil($totalRecords / $recordsPerPage);
}

// Function to fetch students for a specific page
function fetchStudentsForPage($db, $currentPage, $recordsPerPage)
{
    $offset = ($currentPage - 1) * $recordsPerPage;
    $stmt = $db->prepare("SELECT sid, sname FROM students LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':limit', $recordsPerPage, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to fetch a student's schedule
function fetchStudentSchedule($db, $studentId)
{
    $stmt = $db->prepare("SELECT c.cid, c.cname, c.instructor, c.day, c.time FROM enrolled e JOIN courses c ON e.cid = c.cid WHERE e.sid = :studentId");
    $stmt->bindParam(':studentId', $studentId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$adminId = $_SESSION['admin_id'] ?? null;
$adminUsername = $_SESSION['admin_username'] ?? 'Admin';

date_default_timezone_set('America/New_York');
$currentDateTime = date('F j, Y, g:i a');

// Pagination settings
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$recordsPerPage = 10;
$totalPages = getTotalPages($db_connection, $recordsPerPage);
$students = fetchStudentsForPage($db_connection, $currentPage, $recordsPerPage);
?>

<!DOCTYPE html>
<html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View Student Schedules</title>
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
                    <a href="add_course.php" class="btn btn-custom-1 btn-block">Add New Course</a>
                    <a href="edit_courses.php" class="btn btn-custom-1 btn-block">Edit Courses</a>
                    <!-- ... other admin links ... -->
                </div>
                <div class="col-md-8 main-content loader pr-4">
                    <h5>Viewing Student Schedules:</h5>
                    <!-- ... Content ... -->
                    <?php foreach ($students as $student) : ?>
                        <h3>Schedule for <?= htmlspecialchars($student['sname']) ?> (ID: <?= $student['sid'] ?>)</h3>
                        <?php
                        $schedule = fetchStudentSchedule($db_connection, $student['sid']);
                        if (count($schedule) > 0) :
                        ?>
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
                                            <td><?= htmlspecialchars($course['cid']) ?></td>
                                            <td><?= htmlspecialchars($course['cname']) ?></td>
                                            <td><?= htmlspecialchars($course['instructor']) ?></td>
                                            <td><?= htmlspecialchars($course['day']) ?></td>
                                            <td><?= htmlspecialchars($course['time']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <p>No courses enrolled.</p>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <!-- Pagination links -->
                    <!-- Add your pagination logic here -->
                    <div class="pagination">
                        <?php
                        // Display links for previous page, if it's not the first page
                        if ($currentPage > 1) : ?>
                            <a href="?page=<?= $currentPage - 1 ?>">&laquo; Previous</a>
                        <?php endif; ?>

                        <?php
                        // Display link for each page
                        for ($page = 1; $page <= $totalPages; $page++) : ?>
                            <a href="?page=<?= $page ?>" <?= ($page == $currentPage) ? 'class="active"' : '' ?>><?= $page ?></a>
                        <?php endfor; ?>

                        <?php
                        // Display links for next page, if it's not the last page
                        if ($currentPage < $totalPages) : ?>
                            <a href="?page=<?= $currentPage + 1 ?>">&nbsp;Next &raquo;</a>
                        <?php endif; ?>
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