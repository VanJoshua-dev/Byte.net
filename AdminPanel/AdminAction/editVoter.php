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

<?php

// Database connection
$conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM voters";
$result = $conn->query($sql);

$conn->close();
?>

<?php
// Update voters
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['voter_id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $lrn = $_POST['lrn'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Connect to database
    $conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    if (!preg_match('/^\d{12}$/', $lrn)) {
        echo "<script>alert('Invalid LRN format. Please enter a 12-digit number.'); window.history.back();</script>";
        exit();
    }
    // Check if LRN already exists in the database
    $stmt = $conn->prepare("SELECT voter_id FROM voters WHERE lrn = ?");
    $stmt->bind_param("s", $lrn);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($existing_voter_id);
        $stmt->fetch();

        // If LRN belongs to the same voter, allow update; otherwise, display error
        if ($existing_voter_id != $id) {
            echo "Error: LRN is already used by another voter.";
            exit;
        }
    }
    $stmt->close();

    // Check if the changed username already exists and belongs to a different voter
    $stmt = $conn->prepare("SELECT voter_id FROM voters WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($existing_voter_id);
        $stmt->fetch();

        // If username belongs to a different voter, display error
        if ($existing_voter_id != $id) {
            echo "Error: Username is already taken.";
            exit;
        }
    }
    $stmt->close();

    // If password is provided, hash it before updating
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE voters SET firstname=?, lastname=?, lrn=?, username=?, password=? WHERE voter_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $firstname, $lastname, $lrn, $username, $hashed_password, $id);
    } else {
        // If password is not provided, update other fields without changing password
        $sql = "UPDATE voters SET firstname=?, lastname=?, lrn=?, username=? WHERE voter_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $firstname, $lastname, $lrn, $username, $id);
    }

    // Execute update statement
    if ($stmt->execute()) {
        echo "<script>
        alert('Voter updated successfully.');
            window.location.href = '/Byte.net/AdminPanel/Voters.php';
        </script>";
    } else {
        echo "<script>
        Error updating voter.
        </script>";
    }

    $stmt->close();
    $conn->close();
}


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
    <link rel="stylesheet" href="../Styles/VotersStyle.css">
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
<style>
    .container-modal {
        backdrop-filter: blur(10px);
    }
</style>

<body>
    <!-- modal -->
    <!-- #region -->
    <div class="container-modal d-flex flex-column position-fixed z-1 w-100 h-100 pt-1 animate__animated animate__fadeIn"
        id="modal">

        <div class="bg-light w-25 p-3 rounded-2 animate__animated animate__fadeInDown">


            <h3 class="text-center bg-secondary rounded-2">Edit Voter</h3>
            <?php
            if (isset($_GET['id'])) {
                $voter_id = $_GET['id'];
                $conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                $sql = "SELECT * FROM voters WHERE voter_id = '$voter_id'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                } else {
                }
            }

            ?>
            <form action="editVoter.php" method="POST" class="p-3" id="form">
                <div class="mb-3">
                    <input type="hidden" class="form-control w-25" name="voter_id" id="voter_id"
                        value="<?php echo $voter_id; ?>">
                </div>
                <div class="mb-3">
                    <label for="firstname" class="form-label">Firstname</label>
                    <input type="text" class="form-control w-100" name="firstname" id="firstname"
                        value="<?php echo $row['firstname']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="lastname" class="form-label">Lastname</label>
                    <input type="text" class="form-control w-100" name="lastname" id="lastname"
                        value="<?php echo $row['lastname']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="lrn" class="form-label">LRN</label>
                    <input type="number" class="form-control w-100" name="lrn" id="lrn"
                        value="<?php echo $row['lrn']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control w-100" name="username" id="username"
                        value="<?php echo $row['username']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="hashedpassword" class="form-label">Hashed Password</label>
                    <input type="text" class="form-control w-100" name="hashedpassword" id="hashedpassword"
                        value="<?php echo $row['password']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="text" class="form-control w-100" name="password" id="password"
                        placeholder="Type a new password">
                </div>
                <div class="buttons d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" onclick="ThisCLose()">Close</button>
                </div>
            </form>
        </div>
    </div>

    </div>
    <div class="home-container">
        <!-- Header -->
        <header class="home-header">
            <div class="web-logo">
                <img src="../../assets/byte-icon.png" alt="Byte Icon">
            </div>
            <div class="user-profile">
                <img src="../../assets/userprofiles/<?php echo $avatar; ?>" alt="Profile" class="profile-pic">
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
                        <h4 class="text-light"></h4>
                        <div class="breadcrumb-trail d-flex justify-content-center align-items-center">
                            <!-- <i class="fa-solid fa-gauge-high d-flex justify-content-center align-items-center"></i> -->
                            <p class="text-wrap"></p>
                        </div>
                    </div>

                </div>
                <div class="content-body animate__animated animate__fadeIn">

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

</body>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        console.log("Script loaded!");
        let tab = document.querySelector("dashboard"); // Get the sidebar

    });
    //prevent default
    document.addEventListener('DOMContentLoaded', function () {
        let links = document.querySelectorAll('a[href^="#"]');
        links.forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    });
    function ThisCLose() {
        document.querySelector("#modal").classList.remove('animate__fadeIn');
        document.querySelector("#modal").classList.add('animate__fadeOutUp');
        setTimeout(function () {
            window.location.href = "/Byte.net/AdminPanel/Voters.php";
        }, 1000);
    }
</script>

</html>