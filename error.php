<?php
session_start();
include_once 'config.php';

// Get the error message from the query string
$errorMsg = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : 'Unknown error occurred.';

// Define custom error messages
$customErrors = [
    'userexists' => 'User already exists. Please try a different username.',
    'invalidrequest' => 'Invalid request. Please try again.',
    // Add more custom error identifiers and their messages here
];

// Check if the error message matches any custom errors
if (array_key_exists($errorMsg, $customErrors)) {
    $errorMsg = $customErrors[$errorMsg];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .error-container {
            margin-top: 50px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container error-container">
        <h1>An Error Occurred</h1>
        <p class="text-danger"><?php echo $errorMsg; ?></p>
        <a href="index.php" class="btn btn-primary">Go Back to Home</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>