<?php
session_start();
require_once 'connection.php';

$email = $password = "";
$emailErr = $passwordErr = $loginErr = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = trim($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }
    
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = trim($_POST["password"]);
    }
    
    if (empty($emailErr) && empty($passwordErr)) {
        $stmt = $conn->prepare("SELECT emp_id, fullname, email, password, u_id FROM employee WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            
            // Check if password is hashed (assuming bcrypt hash starts with $2y$)
            if (strpos($row["password"], '$2y$') === 0) {
                // Verify with password_verify if it's a hashed password
                $passwordMatch = password_verify($password, $row["password"]);
            } else {
                // Plain text comparison if not hashed
                $passwordMatch = ($password === $row["password"]);
            }
            
            if ($passwordMatch) {
                session_regenerate_id(true); 
                
                $_SESSION["loggedin"] = true;
                $_SESSION["emp_id"] = $row["emp_id"];
                $_SESSION["fullname"] = $row["fullname"];
                $_SESSION["email"] = $row["email"];
                $_SESSION["u_id"] = $row["u_id"];
                
                $success = true;
            } else {
                $loginErr = "Invalid email or password";
            }
        } else {
            $loginErr = "Invalid email or password";
        }
        
        $stmt->close();
    }
}

if (isset($conn) && $conn) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCL Fleet Ledger Login</title>
    
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <video autoplay muted loop class="video-background">
        <source src="assets/vid/clip1.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <div class="loading-screen" id="loading-screen">
        <div class="loader"></div>
        <span>Loading...</span>
    </div>

    <div class="container">
        <div class="logo-container">
            <img src="assets/img/pcl.png" alt="PCL Logo">
            <h1>FLEET LEDGER</h1>
        </div>
        
        <?php if (!empty($loginErr)): ?>
            <div class="alert"><?php echo htmlspecialchars($loginErr); ?></div>
        <?php endif; ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" autocomplete="off">
            <div class="login-container">
                <div class="input-container">
                    <div class="input-wrapper">
                        <input type="email" placeholder="Email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                        <img src="assets/img/log_icon.png" class="input-icon" alt="Email icon">
                    </div>
                    <span class="error"><?php echo $emailErr; ?></span>
                </div>

                <div class="input-container">
                    <div class="input-wrapper">
                        <input type="password" placeholder="Password" id="password" name="password">
                        <img src="assets/img/pas_icon.png" class="input-icon" alt="Password icon">
                    </div>
                    <span class="error"><?php echo $passwordErr; ?></span>
                </div>

                <a href="forgot-password.php" id="forgotLink">Forgot Password?</a>
                <button class="login-button" type="submit">Login</button>
            </div>
        </form>
    </div>

    <?php if ($success): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById("loading-screen").style.display = "flex";
            
            setTimeout(function() {
                window.location.href = "landingPage.php";
            }, 1000);
        });
    </script>
    <?php endif; ?>
</body>
</html>