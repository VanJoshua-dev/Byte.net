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
$host = 'localhost';
$db = 'db_votingsystem';
$user = 'root';
$pass = '1101';

// Create Data Source Name (DSN)
$dsn = "mysql:host=$host;dbname=$db;";

// Options for PDO
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
    PDO::ATTR_EMULATE_PREPARES => false,                  // Disable emulated prepared statements
];

try {
    // Create a new PDO instance
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // If connection fails, display the error message
    echo "Connection failed: " . $e->getMessage();
}



// Fetch data from the database
$sql = "SELECT 
                   votes.vote_id,
                   positions.position_name,
                   candidates.firstname,candidates.middleInitial, candidates.lastName  AS candidate_name,
                   CONCAT(voters.firstname, ' ', voters.lastname) AS voter_name,
                   votes.vote_time
               FROM votes
               JOIN positions ON votes.position_id = positions.position_id
               JOIN candidates ON votes.candidate_id = candidates.candidate_id
               JOIN voters ON votes.voter_id = voters.voter_id
               ORDER BY votes.vote_time DESC";

try {
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}



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
    <link rel="stylesheet" href="Styles/VotesStyle.css">
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
                    <li id="votes"><a href="Votes.php"><i class="fa-solid fa-check-to-slot"></i> Votes</a></li>

                    <div class="sidebar-section">
                        <h5>MANAGE</h5>
                    </div>
                    <li><a href="Voters.php"><i class="fa-solid fa-users"></i> Voters</a></li>
                    <li><a href="Positions.php"><i class="fa-solid fa-chart-simple"></i> Positions</a></li>
                    <li><a href="Candidates.php"><i class="fa-solid fa-user-tie"></i> Candidates</a></li>

                    <div class="sidebar-section">
                        <h5>HISTORY</h5>
                    </div>
                    <li><a href="ElectionHistory.php"><i class="fa-solid fa-clock-rotate-left"></i> Election History</a>
                    </li>

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
                        <h4 class="text-light">Votes</h4>
                        <div class="breadcrumb-trail d-flex justify-content-center align-items-center">
                            <i class="fa-solid fa-gauge-high d-flex justify-content-center align-items-center"></i>
                            <p class="text-nowrap">Home > Votes</p>
                        </div>
                    </div>

                </div>

                <div class="content-body animate__animated animate__fadeIn">

                    <div class="container-fluid d-flex flex-row-reverse justify-content-between p-2 bg-light rounded-top bg-dark">
                        <input type="button" class="btn btn-danger" name="reset" value="🗘 Reset">
                        <input type="text" id="searchInput" class="form-control" style="width: 425px" autocomplete="off"
                            name="search" placeholder="🔍 Search vote by voter ID, position, or candidate name">
                    </div>

                    <div class="table-container bg-light p-3 w-100">
                        <table class="bg-light w-100 rounded-2">
                            <style>
                                
                            </style>
                            <thead class="bg-dark text-light text-center rounded-2">
                                <tr>
                                    <th class="rounded-start">Voter ID</th>
                                    <th>Position</th>
                                    <th>Candidate</th>
                                    <th>Voter</th>
                                    <th class="rounded-end">vote_time</th>
                                </tr>
                            </thead>
                            <style>
                                #tr{
                                    background-color:rgb(181, 181, 181);
                                    cursor: pointer; 
                                }
                            </style>
                            <tbody class="pr-2 text-center">
                                <?php if (count($results) > 0): ?>
                                    <?php foreach ($results as $row): ?>
                                        <tr class="border-bottom" id="tr">
                                            <td><?php echo htmlspecialchars($row['vote_id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['position_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['candidate_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['voter_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['vote_time']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5">No votes found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>



                </div>


            </main>

        </div>
    </div>

    <!-- Bootstrap Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="Scripts/VotesScript.js"></script>

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

    }
    //handle logout
    function Logout() {
        window.location.href = "/Byte.net/vote/logout.php";
    }
</script>

</html>