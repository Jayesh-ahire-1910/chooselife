<?php
session_start();
error_reporting(E_ERROR | E_PARSE);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up form</title>
    <link rel="stylesheet" href="signup.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<?php
require_once("config.php");

if (isset($_POST['submit'])) 
{
    $username = trim($_POST['username']);
    $gender = $_POST['gender'];
    $phn = trim($_POST['phn']);
    $addr = $_POST['addr'];
    $email = trim($_POST['email']);
    $pass = $_POST['pass'];
    $cpass = $_POST['cpass'];
    $aadhar = $_POST['aadhar'];
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'nurseImg/' . $image;
    
    if ($_POST['role'] == 'Patient') 
    {
        $select = mysqli_query($conn, "SELECT * FROM patient WHERE p_email = '$email' AND p_pass = '$pass';") or die("Query Failed");
        if (mysqli_num_rows($select) > 0) 
        {
            $message[] = "User already exists";
        } 
        else 
        {
            if ($pass != $cpass) {
                $message[] = "Confirm Password not matched";
            } elseif ($image_size > 2000000) {
                $message[] = "Image size is too large";
            } else {
                $stmt = $conn->prepare("INSERT INTO patient(p_name, p_gender, p_phone, p_address, p_email, p_pass, p_aadhar) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssissss", $username, $gender, $phn, $addr, $email, $pass, $aadhar);
                $stmt->execute();
                echo "Registration successful";
                
                $otp = rand(100000, 999999);
                //$phone =$phn; // target number; includes ISD
	            $api_key = 'cb736354-8a83-11ed-9158-0200cd936042'; // API Key
	            $req = "https://2factor.in/API/V1/".$api_key."/SMS/".$phn."/".$otp;
                $sms = file_get_contents($req);
	            $sms_status = json_decode($sms, true);
	            if($sms_status['Status'] !== 'Success') 
                {
                    $err['error'] = 'Could not send OTP to ' . $phn;
                }
                else
                {
                    $insert = $conn->prepare("INSERT INTO authentication(mobile_no, otp) VALUES ($phn, $otp)");
                    $insert->execute();
                    $_SESSION['mobile_no'] = $phn;
                    $_SESSION['otp'] = $otp;
                    header("location:verify.php");
                }
                $stmt->close();
                $conn->close();
            }
        }
    } 
    elseif ($_POST['role'] == 'Nurse') 
    {
        $select = mysqli_query($conn, "SELECT * FROM nurse WHERE n_email = '$email' AND n_pass = '$pass'") or die("Query Failed");
        if (mysqli_num_rows($select) > 0) 
        {
            $message[] = 'User already exists';
        } 
        else 
        {
            if ($pass != $cpass) {
                $message[] = "Confirm Password not matched";
            } elseif ($image_size > 2000000) {
                $message[] = "Image size is too large";
            } else {
                $stmt = $conn->prepare("INSERT INTO nurse(n_name, n_gender, n_phone, n_address, n_email, n_pass, n_doc, n_aadhar) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssisssss", $username, $gender, $phn, $addr, $email, $pass, $image, $aadhar);
                $stmt->execute();
                if ($stmt) {
                    move_uploaded_file($image_tmp_name, $image_folder);
                    echo "Registration successful";
                    $otp = rand(100000, 999999);
                    //$phone =$phn; // target number; includes ISD
                    $api_key = 'cb736354-8a83-11ed-9158-0200cd936042'; // API Key
                    $req = "https://2factor.in/API/V1/".$api_key."/SMS/".$phn."/".$otp;
                    $sms = file_get_contents($req);
                    $sms_status = json_decode($sms, true);
                    if($sms_status['Status'] !== 'Success') 
                    {
                        $err['error'] = 'Could not send OTP to ' . $phn;
                    }
                    else
                    {
                        $insert = $conn->prepare("INSERT INTO authentication(mobile_no, otp) VALUES ($phn, $otp)");
                        $insert->execute();
                        $_SESSION['mobile_no'] = $phn;
                        $_SESSION['otp'] = $otp;
                        header("location:verify.php");
                    }
                } else {
                    $message[] = "Registration Unsuccessful";

                    $stmt->close();
                    $conn->close();
                }
            }
        }
    } else {
        $message[] = "Select the role";
    }
}
?>

<section id="nav-bar">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <nav class="navbar navbar-light">
                <img src="images/choose-life-logo.jpg" width="150" height="80" alt="Choose Life Logo">
            </nav>
        </div>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="nurse.php">Nurses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"```php
                    href="index.php">Contact</a>
                </li>
                <li class="prof">
                    <div class="dropdown">
                        <div class="dropimg">
                            <a class="nav-link" href="#profile"><i class="fa fa-user-circle" aria-hidden="true"></i></a>
                        </div>
                        <div class="dropdown-content">
                            <?php
                            if($_SESSION['user_data'])
                            {
                                echo '<a class="drop" href="logout.php">Logout</a>';
                            }
                            else
                            {
                                echo '<a class="drop" href="login.php">Login</a>';
                            }
                            ?>
                        </div>
                    </div>
                </li>
            </ul>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </div>
    </nav>
</section>

<form method="post" action="signup.php" enctype="multipart/form-data">
    <div class="contain">
        <div class="row">
            <div class="col">
                <h3 class="title">Create Account</h3>
                <h6 class="txt">Already have an Account? <a href="login.php">Log In</a></h6>
                <?php
                if(isset($message))
                {
                    foreach ($message as $message) {
                        echo '<div class="message">' . $message . '</div>';
                    }
                }
                ?>
                <label><b>Username</b></label><br>
                <input type="text" name="username" placeholder="Enter Username" required><br>   
                <label><b>Gender</b></label><br>
                <input type="radio" name="gender" value="Male" required> Male
                <input type="radio" name="gender" value="Female" required> Female
                <input type="radio" name="gender" value="Other" required> Other<br>
                <label><b>Phone</b></label><br>
                <input type="text" name="phn" value="+91" size="2" readonly>
                <input type="text" name="phn" placeholder="Phone Number" required> <br>
                <label><b>Address</b></label><br>
                <textarea cols="40" rows="2" value="address" name="addr" placeholder="Enter Address" required></textarea><br>
                <label><b>Email</b></label><br>
                <input type="email" id="email" name="email" placeholder="Enter Email" required><br>
                <label><b>Password</b></label><br>
                <input type="password" id="pass" name="pass" placeholder="Enter Password" required><br>
                <label><b>Confirm password</b></label><br>
                <input type="password" id="cpass" name="cpass" placeholder="Confirm password" required><br>
                <label><b>Aadhar Card Number</b></label><br>
                <input type="text" name="aadhar" placeholder="Enter Aadhar Card Number" required><br>
                <label><b>Sign up as</b></label><br>
                <div class="select-tag">
                    <select name="role" id="role" required>
                        <option value="">Select Role:</option>
                        <option name="patient" value="Patient">Patient</option>
                        <option name="nurse" value="Nurse">Nurse</option>
                    </select>
                </div> 
                <label><b>If Nurse, Upload Aadhar Card for Verification :</b></label><br>
                <input type="file" id="image" name="image" class="box" accept="image/jpg, image/jpeg, image/png"><br>
                <br><input type="submit" value="Submit" name="submit">
            </div>
        </div>
    </div>
</form>
</body>
</html>
