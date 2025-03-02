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
if (isset($_SESSION['popup'])) {
    echo '<div class="w-100 d-flex justify-content-center align-items-center pt-1 position-fixed z-3 top-0 animate__animated animate__fadeIn" id="failed-notif">' .
        $_SESSION['popup']
        . '</div>
        <script>
            setTimeout(() => {
                let notif = document.querySelector("#failed-notif");
                notif.style.opacity = "0";
                notif.style.transform = "translateY(-50px)";
                setTimeout(() => notif.remove(), 300); // Remove from DOM after animation
            }, 2000);
            
        </script>';
    unset($_SESSION["popup"]);
}

?>
<?php
// Database connection
$conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM positions ORDER BY position_id ASC";
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
    <link rel="stylesheet" href="Styles/PositionStyle.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap Components -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/5d9167517f.js" crossorigin="anonymous"></script>

</head>

<body>
    <!-- add position modal -->
    <div class="add-position  w-100  d-flex 
    flex-column justify-content-center align-items-center p-1" id="add-position">
        <div class="bg-light w-25 h-25 p-1 rounded-2">
            <h4 class="title text-center p-1">Add Position</h4>
            <div class="form-contianer w-100  d-flex justify-content-center">
                <form action="AdminAction/AddPosition.php" method="POST"
                    class="form  d-flex flex-column justify-content-center align-items-center">
                    <input type="text" name="position_Name" id="positionName" class="form-control w-100"
                        placeholder="Position Name" autocomplete="off" required>
                    <div class="buttons d-flex flex-row justify-content-end  w-100 gap-2 mt-2">
                        <button type="submit" class="btn btn-primary" id="add-btn">Add</button>
                        <button type="button" class="btn btn-secondary" id="cancel-btn">Cancel</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <!-- edit position modal -->
    <div class="edit-position  w-100  d-flex 
    flex-column justify-content-center align-items-center p-1" id="edit-position">
        <div class="bg-light w-25 h-25 p-1 rounded-2">
            <h4 class="title text-center p-1">Edit Position</h4>
            <div class="form-contianer w-100  d-flex justify-content-center">
                <form action="AdminAction/editPosition.php" method="POST"
                    class="form  d-flex flex-column justify-content-center align-items-center">
                    <input type="hidden" name="current_id" id="current_id" class="form-control w-100"
                        placeholder="New position name." autocomplete="off" required>
                    <input type="text" name="new_posname" id="new_posname" class="form-control w-100"
                        placeholder="New position name." autocomplete="off" required>
                    <div class="buttons d-flex flex-row justify-content-end  w-100 gap-2 mt-2">
                        <button type="submit" class="btn btn-primary" id="edit-btn">Save</button>
                        <button type="button" class="btn btn-secondary" id="cancel-edit-btn">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- delete modal -->
    <div class="delete-position  w-100  d-flex 
    flex-column justify-content-center align-items-center p-1" id="delete-position">
        <div class="bg-light w-25 p-1 rounded-2">
            <h4 class="title text-center p-1"><i class="bi bi-exclamation-triangle-fill p-1 fs-1 text-danger"></i></h4>
            <div class="form-contianer w-100  d-flex justify-content-center">
                <form action="AdminAction/deletePosition.php" method="POST"
                    class="form  d-flex flex-column justify-content-center align-items-center">
                    <input type="hidden" name="del-id" id="del-id" class="form-control w-100"
                        placeholder="New position name." autocomplete="off" required>
                        <h4 class="text-break text-center">
                            
                            Are you sure you want to delete this? This action cannot be undone.
                        </h4>
                    <div class="buttons d-flex flex-row justify-content-end  w-100 gap-2 mt-2">
                        <button type="submit" class="btn btn-danger" id="delete-btn">Confirm</button>
                        <button type="button" class="btn btn-secondary" id="cancel-delete-btn">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
                    <li><a href="votes.php"><i class="fa-solid fa-check-to-slot"></i> Votes</a></li>

                    <div class="sidebar-section">
                        <h5>MANAGE</h5>
                    </div>
                    <li><a href="Voters.php"><i class="fa-solid fa-users"></i> Voters</a></li>
                    <li id="position"><a href="Positions.php"><i class="fa-solid fa-chart-simple"></i> Positions</a>
                    </li>
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
                        <h4 class="text-light">Positions</h4>
                        <div class="breadcrumb-trail d-flex justify-content-center align-items-center">
                            <i class="fa-solid fa-gauge-high d-flex justify-content-center align-items-center"></i>
                            <p class="text-wrap">Home > Positions</p>
                        </div>
                    </div>

                </div>
                <div class="mb-1 p-2 rounded-2 d-flex justify-content-between">
                    <div class="add-btn">
                        <button class="btn btn-primary" id="add-position-btn"><i class="bi bi-plus-circle"></i> Add New
                            Position</button>
                    </div>

                </div>
                <div class="content-body d-flex flex-row flex-wrap animate__animated animate__fadeIn">

                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '
                            <div class="card w-25 text-center shadow-lg p-3 mb-5  rounded">
                                <div class="card-body"> 
                                    <h2 class="card-title">' . $row['position_name'] . '</h2>
                                    <div class="mt-4">
                                        <a href="#" class="editbtn btn btn-success w-25" data-id="'. $row['position_id'] .'" data-name="'. $row['position_name'] .'"><i class="bi bi-pencil-square"></i></a>
                                        <a href="#" class="deletebtn btn btn-danger w-25" data-id ="'. $row['position_id'] . '"><i class="bi bi-trash-fill"></i></a>
                                    </div>
                                    
                                </div>
                            </div>';
                        }
                    } else {
                        echo '<h1>No positions yet.</h1>';
                    }


                    ?>

                </div>

        </div>
        </main>
    </div>
    </div>

    <!-- Bootstrap Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="Scripts/PositionScript.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- AJAX -->
    <script type="text/javascript">


    </script>

</body>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        console.log("Script loaded!");
        let tab = document.querySelector("dashboard"); // Get the sidebar

    });
    $('#cancel-btn').click(function () {
        $('#add-position').css('top', '-150px');
        $('#positionName').val('');
    });
    //open add postion modal
    $('#add-position-btn').click(function () {
        $('#add-position').css('top', '0px');
    });
    function toggleSlidebar() {
        console.log('Open slide bar');
        let sidebar = document.querySelector(".slide-bar"); // Get the sidebar
        sidebar.style.left = "0"; // Move it to the left (visible)
    }
    //open edit modal
    $('.editbtn').click(function () {
        $("#edit-position").css('top', '0px');
        //fill the id
        let posId = $(this).data('id'); // Gets the value of the input, not the id
        let posName = $(this).data('name');
        $('#current_id').val(posId); // Sets the value of new_posname input
        $('#new_posname').val(posName); // Sets the

    });
    //open delete confirmation
    $('.deletebtn').click(function () {
        $("#delete-position").css('top', '0');
        let posId = $(this).data('id'); // Gets the value of the input, not the id
        $('#del-id').val(posId); // Sets the value of delete_id input
        console.log(posId);
    });
    //close delete confirmation
    $('#cancel-delete-btn').click(function () {
        $("#delete-position").css('top', '-1000px');
        $('#del_id').val("");
    });
    $('#cancel-edit-btn').click(function () {
        $("#edit-position").css('top', '-170px');
        let newPosname = $('#new_posname').val("");
    });

    //open edit modal
    // document.querySelector("#editbtn")
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
    function Logout() {
        windows.location.href = "/Byte.net/vote/logout.php";
    }
    function searchPositions() {

    }
</script>

</html>