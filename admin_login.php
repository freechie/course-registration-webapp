<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include_once 'config.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - University System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
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
                <div class="form-container border p-4">
                    <h3 class="text-center">Admin Login Page</h3>
                    <form method="POST" action="admin_authenticate.php">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="form-group">
                            <label for="admin_username">Username:</label>
                            <input type="text" class="form-control" id="admin_username" name="admin_username" placeholder="enter user name" required>
                        </div>
                        <div class="form-group">
                            <label for="admin_password">Password:</label>
                            <input type="password" class="form-control" id="admin_password" name="admin_password" placeholder="enter password" required>
                        </div>
                        <br>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary d-inline-block mb-2 mr-md-2">Login</button>
                            <a href="index.php" class="btn btn-link"><strong>Home</strong></a>
                        </div>
                    </form>
                </div>
            </section>
        </main>

        <footer class="bg-darker text-center text-white container">
            <div class="footer-copyright text-center py-3">
                &nbsp;&copy; <?php echo date("Y"); ?> University System. Super Coders All Rights Reserved.
            </div>
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>