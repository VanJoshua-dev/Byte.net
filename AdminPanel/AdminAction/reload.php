<?php
 $conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

 if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
 }
 $sql = "SELECT * FROM voters";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr class='border-bottom'>";
        echo "<td>" . $row['voter_id'] . "</td>";
        echo "<td>" . $row['firstname'] . "</td>";
        echo "<td>" . $row['lastname'] . "</td>";
        echo "<td>" . $row['lrn'] . "</td>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td class='text-break w-25'>" . $row['password'] . "</td>";
        echo "<td>" . $row['date_created'] . "</td>";
        echo "<td>
                <a href='AdminAction/editVoter.php?id=" . $row['voter_id'] . "' class='btn'><i class='bi bi-pencil-square text-success'></i></a> |
                <a href='AdminAction/deleteVoter.php?id=" . $row['voter_id'] . "' class='btn' onclick=\"return confirm('Are you sure you want to delete this voter? This action cannot be undone.');\">
                <i class='bi bi-trash text-danger'></i></a>
                </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No voters found.</td></tr>";
}
?>