<?php 
    $conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if(isset($_POST['search'])){
        $search = $_POST['search'];
        $sql = "SELECT * FROM voters WHERE voter_id LIKE '%$search%' OR firstname LIKE '%$search%' OR lastname LIKE '%$search%' OR lrn LIKE '%$search%' OR username LIKE '%$search%'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr class='border-bottom' colspan='8' id='tr'>";
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
        }else{
            echo "<tr class='mt-2'>
                    <td colspan='8' class='alert-danger p-2 bg-dark mt-2 text-light text-bold border rounded'>No results found.</td>
                </tr>";
        }
    }

?>