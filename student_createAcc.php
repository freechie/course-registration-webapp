<?php
session_start();
ini_set('display_errors', 1);
include_once 'config.php'; // Include your config file

$error_message = '';

function isUserExisting($pdo, $username)
{

  global $db_connection;
  $stmt = $db_connection->prepare("SELECT COUNT(*) FROM students WHERE username = :username");
  $stmt->bindParam(':username', $username, PDO::PARAM_STR);
  $stmt->execute();
  return $stmt->fetchColumn() > 0;
}

function registerUser($pdo, $sid, $sname, $username, $password)
{
  global $db_connection;
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $db_connection->prepare("INSERT INTO students (sid, sname, username, password) VALUES (:sid, :sname, :username, :password)");
  $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
  $stmt->bindParam(':sname', $sname, PDO::PARAM_STR);
  $stmt->bindParam(':username', $username, PDO::PARAM_STR);
  $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
  return $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $sid = $_POST['sid'] ?? null;
  $sname = $_POST['sname'] ?? '';
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $confirm_password = $_POST['confirm_password'] ?? '';

  if ($password !== $confirm_password) {
    $error_message = "Passwords do not match.";
  } else if (isUserExisting($pdo, $username)) {
    $error_message = "User already exists. Please choose a different username.";
  } else {
    if ($sid && registerUser($pdo, $sid, $sname, $username, $password)) {
      header("Location: student_dashboard.php");
      exit;
    } else {
      $error_message = "Error registering user.";
    }
  }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

  <title>University Shuttle Bus App</title>

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
  <!-- Hero section -->
  <header class="jumbotron display-4 text-white pl-4 text-center">
    <h1>Welcome to the</h1>
    <h1>⏰ Student Schedule Registration System 📖</h1>
    <h3>More updates to functionality coming soon. Please check back again soon.</h3>
    <h1>👷🏾‍♀️🚧👷🏻</h1>
    <br>
    </div>
  </header>

  <main role="main">
    <!-- Registration section -->
    <section class="container">
      <div class="form-container border p-4">
        <h3 class="text-center">Create a new student account</h3>
        <?php if ($error_message) : ?>
          <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form method="POST" action="student_createAcc.php">
          <div class="form-group">
            <label for="name">Name: </label>
            <input type="text" class="form-control" id="sname" name="sname" placeholder="Name" required>
          </div>
          <div class="form-group">
            <label for="username">Username: </label>
            <input type="text" class="form-control" id="username" name="username" placeholder="user name" required>
          </div>
          <div class="form-group">
            <label for="sid">Student ID</label>
            <input type="number" class="form-control" id="sid" name="sid" placeholder="000000" required>
          </div>
          <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="password" required>
          </div>
          <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
          </div>
          <br>
          <div class="form-group">
            <button type="submit" class="btn btn-primary d-inline-block mb-2 mr-md-2">Register</button>
            <a href="index.php" class="btn btn-link"><strong>Home</strong></a>
          </div>
      </div>
      </form>
    </section>


  </main>


  <!-- Footer -->
  <footer class="bg-darker text-center text-white container">
    <div class="footer-copyright text-center py-3">
      <!-- Footer links -->
      <br><br>
      <a href="privacy-policy.php">Privacy Policy</a> &middot; <a href="terms-of-service.php">Terms of Service</a>
      &nbsp;&copy;
      <?php echo date("Y"); ?> University Shuttle Bus App. All Rights Reserved.
    </div>
  </footer>

  </div>
  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
</body>

</html>