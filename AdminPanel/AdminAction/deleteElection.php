<?php
sleep(1);

// Check if ID is set in the URL
if (isset($_GET['id'])) {
    $election_id = $_GET['id'];
    $conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the election exists
    $query = "SELECT * FROM elections WHERE election_id = $election_id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Step 1: Identify positions related to the election
        $positionQuery = "SELECT position_id FROM positions WHERE position_id IN 
                          (SELECT position_id FROM candidates)";
        $positionResult = $conn->query($positionQuery);
        $positions = [];
        while ($row = $positionResult->fetch_assoc()) {
            $positions[] = $row['position_id'];
        }
        $positionIDs = implode(',', $positions);

        // Step 2: Store votes received in election history
        $voteQuery = "SELECT v.position_id, v.candidate_id, COUNT(*) as votes_received 
                      FROM votes v 
                      JOIN candidates c ON v.candidate_id = c.candidate_id 
                      WHERE c.position_id IN ($positionIDs) 
                      GROUP BY v.position_id, v.candidate_id";
        $voteResult = $conn->query($voteQuery);

        while ($vote = $voteResult->fetch_assoc()) {
            $stmt = $conn->prepare("INSERT INTO histories (election_id, position_id, candidate_id, votes_received) 
                                    VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiii", $election_id, $vote['position_id'], $vote['candidate_id'], $vote['votes_received']);
            $stmt->execute();
            $stmt->close();
        }

        // Step 3: Delete related data in correct order
        if (!empty($positionIDs)) {
            $conn->query("DELETE FROM votes WHERE position_id IN ($positionIDs)");
            $conn->query("DELETE FROM candidates WHERE position_id IN ($positionIDs)");
            $conn->query("DELETE FROM positions WHERE position_id IN ($positionIDs)");
        }
        $conn->query("DELETE FROM elections WHERE election_id = $election_id");

        echo "Election archived and deleted successfully.";
    } else {
        echo "No election found.";
    }

    $conn->close();
} else {
    echo "<script>alert('No election ID provided!'); window.history.back();</script>";
}
?>
