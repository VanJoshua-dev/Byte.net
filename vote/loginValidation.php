<?php
    session_start();
    $host = "localhost";
    $user = "root";
    $pass = "1101";
    $dbname = "db_votingsystem";
    
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = $_POST["password"];
    
        // Check Voters Table
        $stmt = $conn->prepare("SELECT voter_id, firstname, lastname, lrn, password, image_path FROM voters WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
    
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($voterID, $firstname, $lastname, $lrn, $hashedPassword, $avatar);
            $stmt->fetch();
    
            if (password_verify($password, $hashedPassword)) {
                $_SESSION["voter_id"] = $voterID;
                $_SESSION["username"] = $username;
                $_SESSION["role"] = "voter";
                $_SESSION["image_path"] = $avatar;
                $_SESSION["firstname"] = $firstname;
                $_SESSION["lastname"] = $lastname;
                $_SESSION["lrn"] = $lrn;
                header("Location: home.php");
                exit();
            }
        }
        $stmt->close();
    
        // Check Admin Table
        $stmt = $conn->prepare("SELECT admin_id, password, avatar FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
    
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($adminId, $hashedPassword, $avatar);
            $stmt->fetch();
    
            if (password_verify($password, $hashedPassword)) {
                $_SESSION["admin_id"] = $adminId;
                $_SESSION["username"] = $username;
                $_SESSION["role"] = "admin";
                $_SESSION["avatar"] = $avatar;
                sleep(2);
                header("Location: /Byte.net/AdminPanel/adminPanel.php");
                exit();
            }
            
        }
        
        $stmt->close();
        $conn->close();
    
        // If user is not found in either table
        $_SESSION["error"] = "Login Failed: Invalid username or password.";
        header("Location: login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <link rel="stylesheet" href="Styles/loginStyle.css">
    <title>Login</title>
</head>
<body>
    
</body>
</html>