<?php
session_start();
$conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['del-id'])) {
        $_SESSION['popup'] = '<div class="alert alert-warning">Missing required fields!</div>';
        header("Location: /Byte.net/AdminPanel/Positions.php");
        exit();
    }
    $posID = $_POST['del-id'];
    try {

        $stmt = $conn->prepare("DELETE FROM positions WHERE position_id = ?");
        $stmt->bind_param("i", $posID);
        if ($stmt->execute()) {
            $_SESSION['popup'] = '<div class="alert alert-success d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill p-2 fs-5"></i>
            <div>Position deleted!</div>
            </div>';
            header("Location: /Byte.net/AdminPanel/Positions.php");
            exit();
        } else {
            $_SESSION['popup'] = '<div class="alert alert-success d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill p-2 fs-5 text-danger"></i>
            <div>Failed to delete position!</div>
            </div>';
            header("Location: /Byte.net/AdminPanel/Positions.php");
            exit();
        }

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