<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <!-- Header -->
    <div class="content-wrap">
        <header>
            <div class="navbar navbar-expand-lg navbar-light padding">
                <div class="col text-left">
                    <a href='student_dash.php' class="btn btn-dark">Student</a>
                </div>
                <div class="col text-right">
                    <a href="index.php" class="btn btn-link" style="color: #000000;">Home</a>
                    <a href="about.php" class="btn btn-link" style="color: #000000;">About</a>
                    <a href="contact.php" class="btn btn-link" style="color: #000000;">Contact</a>
                    <a href="myaccount.php" class="btn btn-outline-dark">My Account</a>
                    <a href="logout.php" class="btn btn-dark">Log out</a>
                </div>
            </div>
        </header>
        <!-- Main Content -->
        <main>
            <div class="row padding main-content">
                <!-- Sidebar -->
                <div class="col-md-4 sidebar">
                    <!-- The date and time can be dynamically generated using PHP -->
                    <h3>Welcome, User</h3>
                    <p>Current Date and Time</p>
                    <p>If no courses registered, notify student</p>
                    <a href="#" class="btn btn-custom btn-block">View My Courses</a>
                    <a href="#" class="btn btn-custom btn-block">Add/Drop Course</a>
                </div>
                <!-- Content next to left sidebar -->
                <div class="col-md-8 main-content loader">
                    <br>
                    <h5>Please select an option to the left to access your Student Portal</h5>
                    <br>
                    <p>Populated tables when options selected from the left should go around here</p>
                </div>
            </div>
        </main>
    </div>
    <!-- Footer -->
    <footer>
        <div class="footer row padding">
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