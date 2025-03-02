<?php
// Database connection
$conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all positions
$positionsQuery = "SELECT * FROM positions ORDER BY position_id ASC";
$positionsResult = $conn->query($positionsQuery);

?>

<?php
$query = "SELECT c.candidate_id, c.firstname, c.middleInitial, c.lastName, 
                                c.gender,p.position_id , p.position_name, c.credibilities, c.platform, c.avatar
                                FROM candidates c
                                JOIN positions p ON c.position_id = p.position_id
                                ORDER BY c.candidate_id ASC";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($candidate = $result->fetch_assoc()) { ?>
        <tr class="text-center" id="tr">
            <td class="rounded-start p-2"><?php echo $candidate['candidate_id'] ?></td>

            <td><?php echo htmlspecialchars($candidate['firstname']) . " " .
                htmlspecialchars($candidate['middleInitial']) . " " .
                htmlspecialchars($candidate['lastName']); ?>
            </td>
            <td><?php echo htmlspecialchars($candidate['gender']); ?></td>
            <td><?php echo htmlspecialchars($candidate['position_name']); ?></td>
            <td class="w-25 text-break">
                <?php echo htmlspecialchars($candidate['credibilities']); ?>
            </td>
            <td class="text-break"><?php echo htmlspecialchars($candidate['platform']); ?>
            </td>
            <td>
                <img src="../assets/userprofiles/<?php echo htmlspecialchars($candidate['avatar']); ?>" alt="Avatar"
                    class="rounded-circle" width="40" height="40">
            </td>
            <td class="rounded-end">
                <a class="editbtn btn btn-success btn-sm" data-id="<?php echo $candidate['candidate_id']; ?>"
                    data-fname="<?php echo $candidate['firstname']; ?>" data-mi="<?php echo $candidate['middleInitial']; ?>"
                    data-lname="<?php echo $candidate['lastName']; ?>" data-gender="<?php echo $candidate['gender']; ?>"
                    data-position="<?php echo $candidate['position_id']; ?>"
                    data-credibilities="<?php echo $candidate['credibilities']; ?>"
                    data-platform="<?php echo $candidate['platform']; ?>" data-avatar="<?php echo $candidate['avatar']; ?>">
                    <i class="bi bi-pencil-square"></i>
                </a>
                <a class="deleteCandidate btn btn-danger btn-sm" data-id="<?php echo $candidate['candidate_id']; ?>">
                    <i class="bi bi-trash-fill"></i>
                </a>
            </td>
        </tr>
    <?php }
} else {
    echo "<tr><td colspan='7' class='text-center text-muted'>No candidates available.</td></tr>";
}
?>