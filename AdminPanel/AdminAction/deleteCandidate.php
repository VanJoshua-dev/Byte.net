<?php
session_start();
$conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['del-id'])) {
        $_SESSION['popup'] = '<div class="alert alert-warning">Missing required fields!</div>';
        header("Location: ../Candidates.php");
        exit();
    }
    $posID = $_POST['del-id'];
    try {

        $stmt = $conn->prepare("DELETE FROM candidates WHERE candidate_id = ?");
        $stmt->bind_param("i", $posID);
        if ($stmt->execute()) {
            $_SESSION['error'] = '<div class="alert alert-success d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill p-2 fs-5"></i>
            <div>Candidate deleted!</div>
            </div>';
            header("Location: ../Candidates.php");
            exit();
        } else {
            $_SESSION['error'] = '<div class="alert alert-success d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill p-2 fs-5 text-danger"></i>
            <div>Failed to delete candidate!</div>
            </div>';
            header("Location: ../Candidates.php");
            exit();
        }

    } catch (mysqli_sql_exception $e) {
        $_SESSION['error'] = '<div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill p-2 fs-4"></i>
            <div>Error: ' . $e->getMessage() . '</div>
        </div>';
        header("Location: ../Candidates.php");
        exit();
    }

} else {
    header('Location: ../Candidates.php');
    exit();
}
?>