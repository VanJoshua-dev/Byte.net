<?php
session_start();
$conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $positionName =  $_POST['position_Name'];
    $checkQuery = "SELECT * FROM positions WHERE position_name = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $positionName);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $_SESSION['popup'] = '<div class="alert alert-danger d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill p-2 fs-4"></i>
        <div>Position already Exist!</div>
        </div>';
        header("Location: /Byte.net/AdminPanel/Positions.php");
        exit();
    } else {
        // Insert new position
        $insertQuery = "INSERT INTO positions (position_name) VALUES (?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("s", $positionName);
        
        if ($stmt->execute()) {
            $_SESSION['popup'] = '<div class="alert alert-success d-flex align-items-center" role="alert">
        <i class="bi bi-check-circle-fill p-2 fs-5"></i>
        <div>Position Added!</div>
        </div>';
        header("Location: /Byte.net/AdminPanel/Positions.php");
        exit();
        } else {
            echo "error";
        }
        
    }
    $stmt->close();
}
$conn->close();

?>