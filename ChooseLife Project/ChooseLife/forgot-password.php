<?php
if (!isset($_SESSION)) {
    session_start();
}
##error_reporting(E_ERROR | E_PARSE);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .navbar {
            background-color: grey;
        }
    </style>
</head>
<body>

<?php

require_once("config.php");

if (isset($_POST['reset_password'])) {
    $email = $_POST['email'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $found = false;

    // Check in the patient table
    $select = mysqli_query($conn, "SELECT * FROM patient WHERE p_email = '" . $email . "' AND p_pass = '" . $old_password . "'") or die('Query failed!');
    if (mysqli_num_rows($select) > 0) {
        $found = true;
        $row = mysqli_fetch_assoc($select);
        // Update the password in the patient table
        $update = mysqli_query($conn, "UPDATE patient SET p_pass = '" . $new_password . "' WHERE p_email = '" . $email . "'");
        if ($update) {
            echo "Password updated successfully for the patient.";
        }
    }

    // Check in the nurse table if not found in the patient table
    if (!$found) {
        $select = mysqli_query($conn, "SELECT * FROM nurse WHERE n_email = '" . $email . "' AND n_pass = '" . $old_password . "'") or die('Query failed!');
        if (mysqli_num_rows($select) > 0) {
            $found = true;
            $row = mysqli_fetch_assoc($select);
            // Update the password in the nurse table
            $update = mysqli_query($conn, "UPDATE nurse SET n_pass = '" . $new_password . "' WHERE n_email = '" . $email . "'");
            if ($update) {
                echo "Password updated successfully for the nurse.";
            }
        }
    }

    if (!$found) {
        echo "Incorrect email or old password.";
    }

    $select->close();
    $conn->close();
}
?>

<section id="nav-bar">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <nav class="navbar navbar-light">
                <img src="images/choose-life-logo.jpg" width="150" height="80" alt="Choose Life Logo">
            </nav>
        </div>
    </nav>
</section>

<form action="forgot-password.php" method="post">
    <div class="login">
        <div class="l1">
            <div class="l2">
                <h3 class="title">Forgot Password</h3>
                <!-- Forgot password form fields -->
                <label class="title"><b>Email</b></label><br>
                <input type="email" name="email" placeholder="Enter email" required/><br>
                <label class="title"><b>Old Password</b></label><br>
                <input type="password" name="old_password" placeholder="Enter Old Password" required/><br>
                <label class="title"><b>New Password</b></label><br>
                <input type="password" name="new_password" placeholder="Enter New Password" required/><br>
                <label class="title"><b>Confirm Password</b></label><br>
                <input type="password" name="confirm_password" placeholder="Confirm New Password" required/><br>
                <input type="submit" value="Reset Password" name="reset_password" /><br>
                <p class="txt">Remember your password? <a href="login.php"><b>Login</b></a></p>
            </div>
        </div>
    </div>
</form>

</body>
</html>
