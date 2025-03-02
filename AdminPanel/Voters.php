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

$sql = "SELECT * FROM voters";
$result = $conn->query($sql);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Admin Panel</title>
    <!-- icon -->
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="Styles/VotersStyle.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap Components -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- MDB icon -->
    <link rel="icon" href="img/mdb-favicon.ico" type="image/x-icon" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <!-- Google Fonts Roboto -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" />
    <!-- MDB -->
    <link rel="stylesheet" href="css/mdb.min.css" />
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
                <button type="button" onclick="LogOut()"><i class="fa-solid fa-sign-out-alt"></i> Logout</button>
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
                    <li id="voters"><a href="Voters.php"><i class="fa-solid fa-users"></i> Voters</a></li>
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
                        <h4 class="text-light">Voters <?php
                        if (isset($_GET["id"])) {
                            $id = $_GET["id"];
                            echo $id;
                        }

                        ?></h4>
                        <div class="breadcrumb-trail d-flex justify-content-center align-items-center">
                            <i class="fa-solid fa-gauge-high d-flex justify-content-center align-items-center"></i>
                            <p class="text-wrap">Home > Voters</p>
                        </div>
                    </div>

                </div>
                <div class="content-body animate__animated animate__fadeIn">
                    <div class="w-100 bg-light d-flex p-2 rounded-top bg-dark">
                        <input type="search" class="form-control w-25" id="liveSearch" name="search" autocomplete="off" placeholder="🔍 Search voter by Name, LRN, ID">
                    </div>
                    <div class="table-container bg-light p-3 w-100">
                        <table class="table-responsive w-100 text-center bg-transparent overflow-auto" id="data-section" >
                            <style>
                               #tr:hover{
                                 background-color:rgb(181, 181, 181);
                                   cursor: pointer;
                                   
                               }
                            </style>
                            <thead class="bg-dark text-light">
                                <th class="rounded-start p-2" >Voter ID</th>
                                <th>Firstname</th>
                                <th>Lastname</th>
                                <th>LRN</th>
                                <th>Username</th>
                                <th >Password</th>
                                <th>Date Created</th>
                                <th class="rounded-end">Action</th>
                            </thead>
                            <tbody id="voterTableBody">
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr class='border-bottom' id='tr'>";
                                        echo "<td class='rounded-start'>" . $row['voter_id'] . "</td>";
                                        echo "<td>" . $row['firstname'] . "</td>";
                                        echo "<td>" . $row['lastname'] . "</td>";
                                        echo "<td>" . $row['lrn'] . "</td>";
                                        echo "<td>" . $row['username'] . "</td>";
                                        echo "<td class='text-break w-25'>" . $row['password'] . "</td>";
                                        echo "<td>" . $row['date_created'] . "</td>";
                                        echo "<td class='rounded-end'>
                            <a href='AdminAction/editVoter.php?id=" . $row['voter_id'] . "' class='btn btn-success'><i class='bi bi-pencil-square '></i></a>
                            <a href='AdminAction/deleteVoter.php?id=" . $row['voter_id'] . "' class='btn btn-danger' onclick=\"return confirm('Are you sure you want to delete this voter? This action cannot be undone.');\">
                            <i class='bi bi-trash'></i></a>
                          </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>No voters found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
        </div>
        </main>
    </div>
    </div>

    <!-- Bootstrap Scripts -->
    <!-- MDB -->
    <script type="text/javascript" src="js/mdb.umd.min.js"></script>
    <!-- Custom scripts -->
    <script type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="adminScript.js"></script>
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- AJAX -->
    <script type="text/javascript">
        $(document).ready(function () {
            $("#liveSearch").keyup(function (e) {
                var input = $(this).val();
                console.log(input);
                if(input != ""){
                    $.ajax({
                        url: "AdminAction/searchVoter.php",
                        type: "POST",
                        data: {
                            search: input
                        },
                        success: function (data) {
                            $("#voterTableBody").html(data);
                        }
                    });
                }else{
                    $("#voterTableBody").load("AdminAction/reload.php");
                }
            });
                
        });
    </script>
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

        //handle Logout

        // handle Create Election

    }
    function LogOut() {
        window.location.href = "/Byte.net/vote/logout.php";
    }
    function OpenModal() {
        let modal = document.querySelector("#Modal");
        modal.style.display = "flex";
    }
</script>

</html>