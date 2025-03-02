
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
// Database connection
$conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT COUNT(*) AS total FROM positions";
$result = $conn->query($sql);

// Display the count
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_positions= $row["total"];
} else {
    echo "No data found.";
}
$sql = "SELECT COUNT(*) AS total FROM candidates";
$result = $conn->query($sql);

// Display the count
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_candidates = $row["total"];
} else {
    echo "No data found.";
}
$sql = "SELECT COUNT(*) AS total FROM voters";
$result = $conn->query($sql);

// Display the count
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_voters = $row["total"];
} else {
    echo "No data found.";
}
//votes
$sql = "SELECT COUNT(*) AS total FROM votes";
$result = $conn->query($sql);

// Display the count
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_votes = $row["total"];
} else {
    echo "No data found.";
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- Font Awesome -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <!-- Google Fonts Roboto -->
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap"
    />
    <!-- MDB -->
    <link rel="stylesheet" href="../css/mdb.min.css" />
    <!-- icon -->
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="Styles/adminstyle.css">
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
                <img src="../assets/userprofiles/<?php   echo $avatar; ?>" alt="Profile" class="profile-pic">
                <div class="user-name ">
                    <span><?php echo $username; ?></span>
                    <span><?php echo $role; ?></span>
                </div>
                
                <button type="button" id="dropdownbtn" onclick="toogleDropdown()" class="border-0 bg-transparent"><i class="fa-solid fa-circle-chevron-down" id="dropdownIcon"></i></button>
            </div>
            <div class="dropdown">
                    <button type="button" onclick="returntoLogin()"><i class="fa-solid fa-sign-out-alt" ></i> Logout</button>
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
                    <li><a href="CreateElection.php"><i class="fa-solid fa-square-poll-horizontal"></i> Start Election</a></li>
                </ul>
            </aside>
            <!-- Main Content -->
            <main class="content">
                <div class="breadcrumb">
                    <button onclick='toggleSlidebar()' class="nav-bar"><i class="fa-solid fa-bars"></i></button>
                    <div class="breadcrumb-wrapper">
                        <h4 class="text-light">Dashboard</h4>
                        <div class="breadcrumb-trail ">
                            <i class="fa-solid fa-gauge-high d-flex justify-content-center align-items-center"></i> 
                            <p class="text-wrap text-center">Home > Dashboard</p>
                        </div>
                    </div>
                    
                </div>

                <div class="content-body animate__animated animate__fadeIn">
                    <!-- Statistics Cards -->
                    <div class="stat-card positions">
                        <div class="d-flex gap-5">
                            <div class="stat-data">
                                <span><?php echo $total_positions; ?></span>
                                <p>No. of Positions</p>
                            </div>
                            <div class="stat-icon"><i class="fa-solid fa-chart-simple icon"></i></div>
                        </div>
                        
                        <a href="Positions.php" class="w-100 text-decoration-none"><div class="stat-footer">More info <i class="fa-solid fa-arrow-right"></i></div></a>
                    </div>

                    <div class="stat-card candidates">
                        <div class="d-flex gap-5">
                        <div class="stat-data">
                                <span><?php echo $total_candidates; ?></span>
                                <p>No. of Candidates</p>
                            </div>
                            <div class="stat-icon"><i class="fa-solid fa-user-tie"></i></div>
                        </div>
                        
                        <a href="Candidates.php" class="w-100 text-decoration-none"><div class="stat-footer">More info <i class="fa-solid fa-arrow-right"></i></div></a>
                    </div>

                    <div class="stat-card voters">
                        <div class="d-flex gap-5">
                            <div class="stat-data">
                                <span><?php echo $total_voters; ?></span>
                                <p>Total Voters</p>
                            </div>
                            <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                        </div>
                       
                        <a href="Voters.php" class="w-100 text-decoration-none"><div class="stat-footer">More info <i class="fa-solid fa-arrow-right"></i></div></a>
                    </div>

                    <div class="stat-card voted">
                        <div class="d-flex gap-5">
                            <div class="stat-data">
                                <span><?php echo $total_votes; ?></span>
                                <p>Total Votes</p>
                            </div>
                            <div class="stat-icon"><i class="fa-solid fa-check-to-slot"></i></div>
                        </div>
                        
                        <a href="Votes.php" class="w-100 text-decoration-none"><div class="stat-footer">More info <i class="fa-solid fa-arrow-right"></i></div></a>
                    </div>
                </div>
                <div class="vote-tally">
                   
                    
                </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap Scripts -->
    <script type="text/javascript" src="../js/mdb.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="Scripts/adminScript.js"></script>
    
</body>
<script>
    
</script>
</html>
