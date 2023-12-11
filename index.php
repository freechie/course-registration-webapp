<?php
ini_set('display_errors', 1); // Turn on  error displaying
ini_set('log_errors', 0); // don't Log errors instead of displaying them
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Database connection using PDO
try {
    // Replace 'your_host', 'your_database', 'username', and 'password' with your actual database details
    $pdo = new PDO('mysql:host=localhost;dbname=finalprojdatabase', 'root', '');

    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Connection is successfully established
    //  echo "Connected successfully";
} catch (PDOException $e) {
    // Handle connection error
    echo "Connection failed: " . $e->getMessage();
}

// CSRF Token validation for form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        // Handle the error, token mismatch
    }
    // Proceed with form processing
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <title>Home</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://cdn.jsdelivr.net/npm/html5shiv@3.7.3/dist/html5shiv.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/respond.js@1.4.2/dest/respond.min.js"></script>
    <![endif]-->
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/stylesheet.css">

</head>


<body>
    <div class="content-wrap">
        <!-- Hero section -->
        <header class="jumbotron display-4 text-white pl-4 text-center">
            <h1>Welcome to the</h1>
            <h1>⏰ Student Schedule Registration System 📖</h1>
            <h3>More updates to functionality coming soon. Please check back again soon.</h3>
            <h1>👷🏾‍♀️🚧👷🏻</h1>
            <br>
        </header>
        <main role="main">
            <section class="container">
                <div class="links-container">
                    <!-- Login section -->
                    <h4><a href="student_login.php">Student Log in</a></h4>
                    <h4><a href="admin_login.php">Admin Log in</a></h4>
                    <br>
                    <h4><a href="student_createAcc.php"><strong>Students Register Here</strong></a></h4>
                    <br>
                </div>

            </section>
            
            <!-- Footer -->
            <footer class="bg-darker text-center text-white container">
                <div class="footer-copyright text-center py-3">
                    <!-- Footer links -->
                    &nbsp;&copy;
                    <?php echo date("Y"); ?> University Student Scheduling App. Super Coders All Rights Reserved.
                </div>
            </footer>
        </div>
    </div>
    </main>


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>

</body>

</html>