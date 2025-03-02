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
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT * FROM elections where is_active = 1";
    $result = $conn->query($query);
    

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
    <link rel="stylesheet" href="Styles/CreateElectionStyle.css">
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
                
                <button type="button" id="dropdownbtn" onclick="toogleDropdown()" class="border-0 bg-transparent"><i class="fa-solid fa-circle-chevron-down" id="dropdownIcon"></i></button>
              
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
                    <button class="close-slide-bar" onclick="close_slidebar()"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <ul>
                    <div class="sidebar-section"><h5>REPORTS</h5></div>
                    <li id="dashboard"><a href="adminPanel.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li>
                    <li><a href="Votes.php"><i class="fa-solid fa-check-to-slot"></i> Votes</a></li>

                    <div class="sidebar-section"><h5>MANAGE</h5></div>
                    <li><a href="Voters.php"><i class="fa-solid fa-users"></i> Voters</a></li>
                    <li><a href="Positions.php"><i class="fa-solid fa-chart-simple"></i> Positions</a></li>
                    <li><a href="Candidates.php"><i class="fa-solid fa-user-tie"></i> Candidates</a></li>

                    <div class="sidebar-section"><h5>HISTORY</h5></div>
                    <li><a href="ElectionHistory.php"><i class="fa-solid fa-clock-rotate-left"></i> Election History</a></li>

                    <div class="sidebar-section"><h5>CONTROL</h5></div>
                    <li id="create-election"><a href="#"><i class="fa-solid fa-square-poll-horizontal"></i> Start Election</a></li>
                </ul>
            </aside>

            <!-- Main Content -->
            <main class="content">
                <div class="breadcrumb">
                    <button onclick='toggleSlidebar()' class="nav-bar"><i class="fa-solid fa-bars"></i></button>
                    <div class="breadcrumb-wrapper">
                        <h4 class="text-light">Start Election</h4>
                        <div class="breadcrumb-trail d-flex justify-content-center align-items-center">
                            <i class="fa-solid fa-gauge-high d-flex justify-content-center align-items-center"></i> 
                            <p class="text-wrap">Home > Start Election</p>
                        </div>
                    </div>
                    
                </div>

                <div class="content-body d-flex flex-row gap-2 h-75 justify-content-center align-items-center">
                   <div class="form-container bg-light rounded p-3 d-flex flex-column w-50 h-100 justify-content-center align-items-center">
                        <h4 class="w-100 bg-dark rounded-2 text-light p-2 text-center">Create Election</h4>
                        <form action="AdminAction/startElection.php" method="POST" class="w-100 h-100">
                            <div class="mb-3">
                                <label for="electionName" class="form-label">Election Name</label>
                                <input type="text" class="form-control w-100" name="electionName" id="electionName" required>
                            </div>
                            <div class="mb-3">
                                <label for="electionDesc" class="form-label">Description</label>
                                <textarea type="text" class="form-control w-100" name="electionDesc" id="electionDesc" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="endDate" class="form-label">End Date</label>
                                <input type="date" class="form-control" name="electionEndDate" id="endDate" required>
                            </div>
                            <button class="btn btn-primary">Start Election</button>
                        </form>
                   </div>
                   <div class="ongoing-election bg-light rounded p-3 d-flex flex-column w-25 h-100 justify-content-center align-items-center">
                    <h4 class="w-100 bg-dark rounded-2 text-light p-2 text-center">Ongoing Election</h4>
                    <div class="w-100 h-100  overflow-auto" id="ongoing-election">
                        <?php
                        if($result->num_rows > 0){
                            while($election = $result->fetch_assoc()){
                                echo "<div class='mb-3'>";
                                echo "<h5>". $election['title']. "</h5>";
                                echo "<p>". $election['description']. "</p>";
                                echo "<p>Start Date: ". $election['start_date']. "</p>";
                                echo "<p>End Date: ". $election['end_date']. "</p>";
                                echo "<a href='AdminAction/deleteElection.php?id=". $election['election_id']. "' class='btn btn-danger'>Close election</a>";
                                echo "</div>";
                            }
                        }else{
                            echo "<p class='text-center'>No ongoing elections found.</p>";
                        }
                        
                        
                        ?>
                    </div>
                   </div>
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
    function toogleDropdown(){
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

        //logou
        function Logout() {
            window.location.href = "/Byte.net/vote/logout.php";
        }
       
    }
</script>
</html>
