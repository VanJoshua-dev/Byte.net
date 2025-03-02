<?php
session_start();
$conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the voter is logged in
if (!isset($_SESSION['voter_id'])) {
    die("Voter ID not found. Please log in.");
}

if (!isset($_POST['votes']) || empty($_POST['votes'])) {
    die("No votes submitted.");
}

$voter_id = $_SESSION['voter_id'];
$votes = $_POST['votes'];

foreach ($votes as $position_name => $candidate_id) {
    if (!empty($candidate_id)) {
        // Fetch position_id from position_name
        $stmt = $conn->prepare("SELECT position_id FROM positions WHERE position_name = ?");
        $stmt->bind_param("s", $position_name);
        $stmt->execute();
        $stmt->bind_result($position_id);
        $stmt->fetch();
        $stmt->close();

        if (!$position_id) {
            die("Invalid position: " . htmlspecialchars($position_name));
        }

        // Insert vote into votes table
        $stmt = $conn->prepare("INSERT INTO votes (voter_id, candidate_id, position_id) VALUES (?, ?, ?)");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("iii", $voter_id, $candidate_id, $position_id);
        if (!$stmt->execute()) {
            die("Error executing statement: " . $stmt->error);
        }
        $stmt->close();
    }
}

$conn->close();
header("Location: thank_you.php");
exit();
?>
