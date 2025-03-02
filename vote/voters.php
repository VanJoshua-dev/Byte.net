<?php
session_start();
if (!isset($_SESSION["admin_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: /Byte.net/vote/login.php");
    exit();
}
$avatar = $_SESSION["avatar"];
$username = $_SESSION["username"];
$role = $_SESSION["role"];

// Database connection
$conn = new mysqli("localhost", "root", "1101", "db_votingsystem");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="Styles/VotersStyle.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="home-container">
        <header class="home-header">
            <div class="web-logo">
                <img src="../assets/byte-icon.png" alt="Byte Icon">
            </div>
            <div class="user-profile">
                <img src="../assets/userprofiles/<?php echo $avatar; ?>" alt="Profile" class="profile-pic">
                <div class="user-name">
                    <span><?php echo $username; ?></span>
                    <span><?php echo $role; ?></span>
                </div>
                <button type="button" onclick="LogOut()">Logout</button>
            </div>
        </header>

        <div class="main-content">
            <aside class="slide-bar">
                <ul>
                    <li><a href="adminPanel.php">Dashboard</a></li>
                    <li><a href="Voters.php">Voters</a></li>
                </ul>
            </aside>

            <main class="content">
                <h4>Voters</h4>
                <div>
                    <input type="text" id="search" class="form-control" placeholder="Search voter by name or LRN">
                    <button type="button" onclick="searchVoters()">Search</button>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Voter ID</th>
                                <th>Firstname</th>
                                <th>Lastname</th>
                                <th>LRN</th>
                                <th>Username</th>
                                <th>Date Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="voters-table">
                            <?php
                            $sql = "SELECT * FROM voters";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>{$row['voter_id']}</td>";
                                    echo "<td>{$row['firstname']}</td>";
                                    echo "<td>{$row['lastname']}</td>";
                                    echo "<td>{$row['lrn']}</td>";
                                    echo "<td>{$row['username']}</td>";
                                    echo "<td>{$row['date_created']}</td>";
                                    echo "<td><a href='editVoter.php?id={$row['voter_id']}'>Edit</a> | <a href='deleteVoter.php?id={$row['voter_id']}' onclick='return confirm('Are you sure?')'>Delete</a></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7'>No voters found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <script>
        function searchVoters() {
            let searchValue = document.getElementById("search").value;
            $.ajax({
                url: "searchVoters.php",
                method: "GET",
                data: { search: searchValue },
                success: function(response) {
                    $("#voters-table").html(response);
                }
            });
        }
    </script>
</body>
</html>
