<?php 
$conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['electionName']);
    $description = trim($_POST['electionDesc']);
    $endDate = $_POST['electionEndDate'];
    $is_active = 1;

    // Check if an election with the same title exists
    $check_stmt = $conn->prepare("SELECT * FROM elections WHERE title = ?");
    $check_stmt->bind_param("s", $title);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Election title already exists. Choose a different name.'); window.location.href='../CreateElection.php';</script>";
        exit();
    }

    $check_stmt->close();

    // Ensure only one active election at a time
    $conn->query("UPDATE elections SET is_active = 0 WHERE is_active = 1");

    // Insert new election
    $query = "INSERT INTO elections (title, description, end_date, is_active) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $title, $description, $endDate, $is_active);

    if ($stmt->execute()) {
        echo "<script>alert('Election Created Successfully!'); window.location.href='../CreateElection.php';</script>";
    } else {
        echo "<script>alert('Error: Could not create election.'); window.location.href='../CreateElection.php';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
