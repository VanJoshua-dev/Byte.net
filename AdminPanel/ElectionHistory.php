<?php
session_start();
if (!isset($_SESSION["admin_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: /Byte.net/vote/login.php");
    exit();
}
$avatar = $_SESSION["avatar"];
$username = $_SESSION["username"];
$role = $_SESSION["role"];
?>
<?php
$conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch election history
$sql = "SELECT h.election_id, e.title AS election_name, p.position_name, c.firstname, c.middleInitial, c.lastName AS candidate_name, h.votes_received, 
        (SELECT MAX(votes_received) FROM histories WHERE election_id = h.election_id AND position_id = h.position_id) AS max_votes
        FROM histories h
        JOIN elections e ON h.election_id = e.election_id
        JOIN candidates c ON h.candidate_id = c.candidate_id
        JOIN positions p ON h.position_id = p.position_id
        ORDER BY h.election_id, h.position_id, h.votes_received DESC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- icon -->
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="Styles/ElectionHistoryStyle.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap Components -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/5d9167517f.js" crossorigin="anonymous"></script>

</head>

<body>

    <div class="home-container">
        <!-- Header -->
        <header class="home-header">
            <div class="web-logo">
                <img src="../assets/byte-icon.png" alt="Byte Icon">
            </div>
            <div class="user-profile">
                <img src="../assets/userprofiles/<?php echo $avatar; ?>" alt="Profile" class="profile-pic">
                <div class="user-name ">
                    <span><?php echo $username; ?></span>
                    <span><?php echo $role; ?></span>
                </div>

                <button type="button" id="dropdownbtn" onclick="toogleDropdown()" class="border-0 bg-transparent"><i
                        class="fa-solid fa-circle-chevron-down" id="dropdownIcon"></i></button>

            </div>
            <div class="dropdown">
                <button type="button" onclick="Logout()"><i class="fa-solid fa-sign-out-alt"></i> Logout</button>
            </div>
        </header>

        <div class="main-content">
            <!-- Sidebar -->
            <aside class="slide-bar">
                <div class="nav-title text-center text-light">
                    <h4>Admin Panel</h4>
                    <button class="close-slide-bar" onclick="close_slidebar()"><i
                            class="fa-solid fa-xmark"></i></button>
                </div>
                <ul>
                    <div class="sidebar-section">
                        <h5>REPORTS</h5>
                    </div>
                    <li id="dashboard"><a href="adminPanel.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
                    </li>
                    <li><a href="Votes.php"><i class="fa-solid fa-check-to-slot"></i> Votes</a></li>

                    <div class="sidebar-section">
                        <h5>MANAGE</h5>
                    </div>
                    <li><a href="Voters.php"><i class="fa-solid fa-users"></i> Voters</a></li>
                    <li><a href="Positions.php"><i class="fa-solid fa-chart-simple"></i> Positions</a></li>
                    <li><a href="Candidates.php"><i class="fa-solid fa-user-tie"></i> Candidates</a></li>

                    <div class="sidebar-section">
                        <h5>HISTORY</h5>
                    </div>
                    <li id="election-history"><a href="#"><i class="fa-solid fa-clock-rotate-left"></i> Election
                            History</a></li>

                    <div class="sidebar-section">
                        <h5>CONTROL</h5>
                    </div>
                    <li><a href="CreateElection.php"><i class="fa-solid fa-square-poll-horizontal"></i> Start
                            Election</a></li>
                </ul>
            </aside>

            <!-- Main Content -->
            <main class="content">
                <div class="breadcrumb">
                    <button onclick='toggleSlidebar()' class="nav-bar"><i class="fa-solid fa-bars"></i></button>
                    <div class="breadcrumb-wrapper">
                        <h4 class="text-light">History</h4>
                        <div class="breadcrumb-trail d-flex justify-content-center align-items-center">
                            <i class="fa-solid fa-gauge-high d-flex justify-content-center align-items-center"></i>
                            <p class="text-wrap">Home > History</p>
                        </div>
                    </div>

                </div>

                <div class="content-body animate__animated animate__fadeIn">
                    <h2 class="text-center mb-4">📜 Election History</h2>

                    <?php
                    if ($result->num_rows > 0) {
                        $currentElection = "";
                        $currentPosition = "";

                        while ($row = $result->fetch_assoc()) {
                            if ($currentElection !== $row['election_name']) {
                                if ($currentElection !== "")
                                    echo "</div></div>"; // Close previous election div
                                echo "<div class='card mb-4'><div class='card-header text-center'><h3>" . htmlspecialchars($row['election_name']) . "</h3></div>";
                                echo "<div class='card-body'>";
                                $currentElection = $row['election_name'];
                            }

                            if ($currentPosition !== $row['position_name']) {
                                if ($currentPosition !== "")
                                    echo "</ul></div>"; // Close previous position list
                                echo "<div class='mb-3'>";
                                echo "<h4>🏅 " . htmlspecialchars($row['position_name']) . "</h4>";
                                echo "<ul class='list-group'>";
                                $currentPosition = $row['position_name'];
                            }

                            // Highlight the winner with 🏆
                            $winnerIcon = ($row['votes_received'] == $row['max_votes']) ? "🏆" : "❌";

                            ?>

                            <li class="list-group-item d-flex justify-content-between">
                                <span><?php echo htmlspecialchars($row['candidate_name']); ?></span>
                                <span>Votes: <?php echo $row['votes_received']; ?>         <?php echo $winnerIcon; ?></span>
                            </li>

                        <?php }

                        echo "</ul></div></div></div>"; // Close last election div
                    } else {
                        echo "<div class='alert alert-warning text-center'>No election history available.</div>";
                    }
                    ?>
                </div>

        </div>
        </main>
    </div>
    </div>

    <!-- Bootstrap Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="adminScript.js"></script>

</body>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        console.log("Script loaded!");
        let tab = document.querySelector("dashboard"); // Get the sidebar

    });
    function toggleSlidebar() {
        console.log('Open slide bar');
        let sidebar = document.querySelector(".slide-bar"); // Get the sidebar
        sidebar.style.left = "0"; // Move it to the left (visible)
    }
    function close_slidebar() {
        console.log('Close slide bar');
        let sidebar = document.querySelector(".slide-bar"); // Get the sidebar
        sidebar.style.left = "-500px"; // Move it to the left (visible)
    }
    function toogleDropdown() {
        let dropdown = document.querySelector(".dropdown");
        let btn = document.querySelector("#dropdownbtn");

        // Check if dropdown is currently visible
        let isVisible = dropdown.style.display === "flex";

        if (isVisible) {
            dropdown.style.display = "none";
            dropdown.style.opacity = 0;
            btn.style.transform = "rotate(0deg)";
        } else {
            dropdown.style.display = "flex";
            dropdown.style.opacity = 1;
            btn.style.transform = "rotate(-180deg)";
        }

        // Toggle icon

    }
    //Logout
    function Logout() {
        // Redirect to login page
        window.location.href = "/Byte.net/vote/logout.php";
    }
</script>

</html>
<?php
$conn->close();
?>