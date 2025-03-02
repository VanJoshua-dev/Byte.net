<?php
// Check if ID is set in the URL

sleep(1);
if (isset($_GET['id'])) {
    $voter_id = $_GET['id'];
    $conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "DELETE FROM voters WHERE voter_id = " . $voter_id;
    $result = $conn->query($sql);
    header("Location: /Byte.net/AdminPanel/Voters.php");
} else {
    echo "<script>alert('No voter ID provided!'); window.history.back();</script>";
}
?>