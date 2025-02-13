<?php
session_start();
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database Connection
$conn = new mysqli("localhost", "root", "", "manit_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle AJAX OTP request
if (isset($_POST['ajax_send_otp'])) {
    $email = $_POST['email'];
    $_SESSION['email'] = $email;

    // Check if email already exists
    $check_email = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($check_email->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email already registered! Please log in."]);
        exit();
    }

    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;

    // Send OTP using PHPMailer
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = 'smtp.sendgrid.net';
    $mail->SMTPAuth = true;
    $mail->Username = 'apikey';
    $mail->Password = 'api_key'; // Replace with actual API Key
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('24112011106@stu.manit.ac.in', 'MANIT Portal');
    $mail->addAddress($email);
    $mail->Subject = "Your OTP Code";
    $mail->Body = "Your OTP is: $otp. It is valid for 10 minutes.";

    if ($mail->send()) {
        echo json_encode(["status" => "success", "message" => "OTP sent successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error sending OTP: " . $mail->ErrorInfo]);
    }
    exit();
}

// Verify OTP
if (isset($_POST['verify_otp'])) {
    $entered_otp = $_POST['otp'];
    if ($entered_otp == $_SESSION['otp']) {
        $email = $_SESSION['email'];
        $conn->query("INSERT INTO users (email) VALUES ('$email')");
        $_SESSION['loggedin'] = true;
        $_SESSION['user_email'] = $email;
        echo "<script>alert('OTP verified! Redirecting to dashboard...'); window.location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('Invalid OTP! Please try again.');</script>";
    }
}

// Logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>MANIT Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            box-shadow: 0px 0px 10px gray;
            border-radius: 10px;
        }
        .tab {
            display: flex;
            justify-content: space-around;
            background: #007bff;
            border-radius: 10px;
            padding: 10px;
            color: white;
        }
        .tab button {
            width: 50%;
            padding: 10px;
            border: none;
            cursor: pointer;
            background: none;
            color: white;
            font-size: 16px;
        }
        .tab .active {
            background: white;
            color: #007bff;
            font-weight: bold;
            border-radius: 5px;
        }
        input, button {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .hidden {
            display: none;
        }
        @media (max-width: 600px) {
            .container {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            <h2>Welcome to Your Dashboard</h2>
            <p>Your Email: <?php echo $_SESSION['user_email']; ?></p>
            <form method="post" action="index.php">
                <button type="submit" name="logout">Logout</button>
            </form>
        <?php else: ?>
            <div class="tab">
                <button id="registerTab" class="active" onclick="showRegister()">Register</button>
                <button id="loginTab" onclick="showLogin()">Login</button>
            </div>

            <div id="registerSection">
                <h3>Register</h3>
                <form id="otpForm">
                    <input type="email" id="emailInput" name="email" placeholder="Enter Email" required><br>
                    <button type="button" id="sendOtpBtn">Send OTP</button>
                </form>

                <div id="otpDiv" class="hidden">
                    <form method="post">
                        <input type="text" name="otp" placeholder="Enter OTP" required><br>
                        <button type="submit" name="verify_otp">Submit</button>
                    </form>
                </div>
            </div>

            <div id="loginSection" class="hidden">
                <h3>Login</h3>
                <form method="post">
                    <input type="email" name="email" placeholder="Email" required><br>
                    <input type="password" name="password" placeholder="Password" required><br>
                    <button type="submit" name="login">Login</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script>
    document.getElementById("sendOtpBtn").addEventListener("click", function() {
        var email = document.getElementById("emailInput").value;
        if (email === "") {
            alert("Please enter an email.");
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "index.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.status === "success") {
                    alert(response.message);
                    document.getElementById("otpDiv").style.display = "block";
                    document.getElementById("sendOtpBtn").style.display = "none";
                    document.getElementById("emailInput").readOnly = true;
                } else {
                    alert(response.message);
                }
            }
        };
        xhr.send("ajax_send_otp=1&email=" + encodeURIComponent(email));
    });

    function showRegister() {
        document.getElementById("registerTab").classList.add("active");
        document.getElementById("loginTab").classList.remove("active");
        document.getElementById("registerSection").style.display = "block";
        document.getElementById("loginSection").style.display = "none";
    }

    function showLogin() {
        document.getElementById("registerTab").classList.remove("active");
        document.getElementById("loginTab").classList.add("active");
        document.getElementById("registerSection").style.display = "none";
        document.getElementById("loginSection").style.display = "block";
    }
</script>

</body>
</html>
