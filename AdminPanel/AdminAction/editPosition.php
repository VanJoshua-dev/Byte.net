<?php
session_start();
$conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['current_id']) || !isset($_POST['new_posname'])) {
        $_SESSION['popup'] = '<div class="alert alert-warning">Missing required fields!</div>';
        header("Location: /Byte.net/AdminPanel/Positions.php");
        exit();
    }
    $posID = $_POST['current_id'];
    $newPosName = $_POST['new_posname'];
    try {
        $checkSql = "SELECT position_id FROM positions WHERE position_name = ? AND position_id != ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("si", $newPosName, $posID);
        $checkStmt->execute();
        $checkStmt->store_result();
        if ($checkStmt->num_rows > 0) {
            $_SESSION['popup'] = '<div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill p-2 fs-4"></i>
            <div>Position already Exist!</div>
            </div>';
            header("Location: /Byte.net/AdminPanel/Positions.php");
            exit();
        }
        $sql = "UPDATE positions SET position_name = ? WHERE position_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $newPosName, $posID);
        $stmt->execute();
        $_SESSION['popup'] = '<div class="alert alert-success d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill p-2 fs-5"></i>
            <div>Position Updated!</div>
            </div>';
        header("Location: /Byte.net/AdminPanel/Positions.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        $_SESSION['popup'] = '<div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill p-2 fs-4"></i>
            <div>Error: ' . $e->getMessage() . '</div>
        </div>';
        header("Location: /Byte.net/AdminPanel/Positions.php");
        exit();
    }

} else {
    header('Location: positions.php');
    exit();
}


?>