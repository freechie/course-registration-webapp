<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: student_login.php");
    exit;
}

$studentId = $_SESSION['sid'] ?? null;
$userName = $_SESSION['username'] ?? 'Guest';

// Set the default timezone if necessary
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
                <!-- Sidebar -->
                <div class="col-md-4 sidebar pl-4">
                    <h5>Welcome, <?php echo htmlspecialchars($userName); ?></h5>
                    <p><?php echo $currentDateTime; ?></p>
                    <a href="view_current_schedule.php" class="btn btn-custom btn-block">View My Courses</a>
                    <a href="addCourse.php" class="btn btn-custom-1 btn-block">Add Course</a>
                    <a href="dropCourse.php" class="btn btn-custom-2 btn-block">Drop Course</a>
                </div>
                <!-- Content next to left sidebar -->
                <div class="col-md-8 main-content loader pr-4">
                    <br>
                    <h5>Please select an option to the left to access your Student Portal Functions</h5>
                </div>
            </div>
        </main>
    </div>
    <!-- Footer -->
    <footer>
        <div class="footer row padding pr-4">
            <div class="col text-right">
                <p>Copyright &copy; 2022 Company Name.</p>
            </div>
        </div>

    </footer>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>