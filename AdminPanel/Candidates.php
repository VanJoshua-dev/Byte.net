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

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all positions
$positionsQuery = "SELECT * FROM positions ORDER BY position_id ASC";
$positionsResult = $conn->query($positionsQuery);

if (isset($_SESSION["error"])) {
    echo '<div class="w-100 d-flex justify-content-center align-items-center pt-1 position-fixed z-3 top-0 animate__animated animate__fadeIn" id="failed-notif">
    ' . $_SESSION["error"] . '
</div>
<script>
    setTimeout(() => {
        let notif = document.querySelector("#failed-notif");
        notif.style.opacity = "0";
        notif.style.transform = "translateY(-50px)";
        setTimeout(() => notif.remove(), 300); // Remove from DOM after animation
    }, 2000);
    
</script>';
    unset($_SESSION["error"]); // Clear the error after displaying it
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
    <link rel="stylesheet" href="Styles/CandidatesStyle.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap Components -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/5d9167517f.js" crossorigin="anonymous"></script>

</head>

<body>
    <!-- add candidate modal -->
    <div class="add-candidate-modal w-100 h-100  position-fixed z-3 d-flex flex-column justify-content-center align-items-center"
        id="AddModal">
        <div class="form-container bg-light rounded p-2">
            <h4 class="text-center text-light p-2 bg-dark rounded ">Add Candidate</h4>
            <form action="AdminAction/addCandidate.php" method="POST" enctype="multipart/form-data" class=" p-2">
                <div>
                    <label for="firstName">Firstname</label>
                    <input type="text" class="form-control w-100" id="firstName" name="firstName" required>
                </div>
                <div>
                    <label for="middleInitial">Middle initial</label>
                    <input type="text" class="form-control w-100" id="middleInitial" name="middleInitial" required>
                </div>
                <div>
                    <label for="lastName">Lastname</label>
                    <input type="text" class="form-control w-100" id="lastName" name="lastName" required>
                </div>
                <div class="form-group">
                    <label for="genderSelect">Gender</label>
                    <select class="form-select" id="genderSelect" name="gender" required>
                        <option value="" disabled selected>--Select a gender--</option>
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                        <option value="Other">Other</option>
                        <option value="prefer not to say">Prefer not to say</option>
                    </select>
                </div>
                <div>
                    <label for="selectedPosition">Position</label>
                    <select class="form-select" id="selectedPosition" name="selectedPosition" required>
                        <option value="" disabled selected>--Select a position--</option>
                        <?php
                        while ($position = $positionsResult->fetch_assoc()) {
                            echo "<option value='" . $position['position_id'] . "'>" . $position['position_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label for="credibility">Credibility</label>
                    <textarea type="text" class="form-control w-100" id="credibility" name="credibility"
                        required></textarea>
                </div>
                <div>
                    <label for="platform">Platform</label>
                    <textarea type="text" class="form-control w-100" id="platform" name="platform" required></textarea>
                </div>
                <div class="image">
                    <label class="" for="image">Image</label>
                    <input type="file" class="form-control mb-3" id="image" name="image">
                    <!-- save the file into ../assets/userprofiles/ -->
                </div>
                <div class="buttons w-100 d-flex flex-row justify-content-end p-2 gap-2">
                    <button type="submit" class="btn btn-primary">Add Candidate</button>
                    <button type="button" class="btn btn-secondary" id="cancelAddCandidate">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <!-- edit candidate modal -->
    <div class="edit-candidate-modal w-100 h-100  position-fixed z-3 d-flex flex-column justify-content-center align-items-center"
        id="EditModal">
        <div class="form-container bg-light rounded p-2">
            <h4 class="text-center text-light p-2 bg-dark rounded ">Edit Candidate</h4>
            <form action="AdminAction/editCandidate.php" method="POST" enctype="multipart/form-data" class=" p-2">
                <input type="hidden" class="form-control" id="candidateID" name="candidateID">
                <div>
                    <label for="newFirstName">Firstname</label>
                    <input type="text" class="form-control w-100" id="newFirstName" name="newFirstName" required>
                </div>
                <div>
                    <label for="newMiddleInitial">Middle initial</label>
                    <input type="text" class="form-control w-100" id="newMiddleInitial" name="newMiddleInitial"
                        required>
                </div>
                <div>
                    <label for="newLastName">Lastname</label>
                    <input type="text" class="form-control w-100" id="newLastName" name="newLastName" required>
                </div>
                <div class="form-group">
                    <label for="newGenderSelect">Gender</label>
                    <select class="form-select" id="newGenderSelect" name="newGenderSelect" required>
                        <option value="" disabled selected>--Select a gender--</option>
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                        <option value="Other">Other</option>
                        <option value="prefer not to say">Prefer not to say</option>
                    </select>
                </div>
                <div>
                    <label for="newSelectedPosition">Position</label>
                    <select class="form-select" id="newSelectedPosition" name="newSelectedPosition" required>
                        <option value="" disabled selected>--Select a position--</option>
                        <?php
                        $conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

                        // Check connection
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Fetch all positions
                        $positionsQuery1 = "SELECT * FROM positions ORDER BY position_id ASC";
                        $positionsResult1 = $conn->query($positionsQuery1);
                        while ($position = $positionsResult1->fetch_assoc()) {
                            echo "<option value='" . $position['position_id'] . "'>" . $position['position_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label for="newCredibility">Credibility</label>
                    <textarea type="text" class="form-control w-100" id="newCredibility" name="newCredibility"
                        required></textarea>
                </div>
                <div>
                    <label for="newPlatform">Platform</label>
                    <textarea type="text" class="form-control w-100" id="newPlatform" name="newPlatform"
                        required></textarea>
                </div>
                <div class="newImage">
                    <label class="" for="newImage">Image</label>
                    <input type="file" class="form-control mb-3" id="newImage" name="newImage">
                    <!-- save the file into ../assets/userprofiles/ -->
                </div>
                <div class="buttons w-100 d-flex flex-row justify-content-end p-2 gap-2">
                    <button type="submit" class="btn btn-primary">Update Candidate</button>
                    <button type="button" class="btn btn-secondary" id="cancelEditCandidate">Cancel</button>
                </div>
            </form>
        </div>
    </div>
     <!-- delete modal -->
     <div class="delete-candidate position-fixed w-100 h-100 d-flex 
    flex-column justify-content-center align-items-center p-1" id="delete-candidate">
        <div class="bg-light w-25 p-1 rounded-2">
            <h4 class="title text-center p-1"><i class="bi bi-exclamation-triangle-fill p-1 fs-1 text-danger"></i></h4>
            <div class="form-contianer w-100  d-flex justify-content-center">
                <form action="AdminAction/deleteCandidate.php" method="POST"
                    class="form  d-flex flex-column justify-content-center align-items-center">
                    <input type="hidden" name="del-id" id="del-id" class="form-control w-100"
                        placeholder="New position name." autocomplete="off" required>
                        <h4 class="text-break text-center">
                            
                            Are you sure you want to continue this? This action cannot be undone.
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
                    <li><a href="Votes.php"><i class="fa-solid fa-check-to-slot"></i> Votes</a></li>

                    <div class="sidebar-section">
                        <h5>MANAGE</h5>
                    </div>
                    <li><a href="Voters.php"><i class="fa-solid fa-users"></i> Voters</a></li>
                    <li><a href="Positions.php"><i class="fa-solid fa-chart-simple"></i> Positions</a></li>
                    <li id="candidates"><a href="Candidates.php"><i class="fa-solid fa-user-tie"></i> Candidates</a>
                    </li>

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
                        <h4 class="text-light">Candidates</h4>
                        <div class="breadcrumb-trail d-flex justify-content-center align-items-center">
                            <i class="fa-solid fa-gauge-high d-flex justify-content-center align-items-center"></i>
                            <p class="text-wrap">Home > Candidates</p>
                        </div>
                    </div>

                </div>

                <div class="content-body d-flex flex-nowrap flex-column">
                    <div class="filter-container d-flex justify-content-start gap-2 bg-light p-3 bg-dark rounded-top">
                        <input type="search" id="searchInput" class="form-control w-25"
                            placeholder="🔍 Search by candidate name, president name">
                        <button type="button" class="btn btn-primary" id="AddCandidate"><i
                                class="bi bi-plus-circle p-2"></i>Add
                            Candidate</button>
                    </div>
                    <div class="candidate-data bg-light p-3 h-100" id="candidate-container">
                        <table class="w-100">
                            <style>
                                #tr:hover {
                                    background-color: rgb(181, 181, 181);
                                    cursor: pointer;

                                }
                            </style>
                            <thead class="bg-dark text-light text-center">
                                <tr>
                                    <th class="text-break p-2 rounded-start">Candidate ID</th>
                                    <th class="text-break">Fullname</th>
                                    <th>Gender</th>
                                    <th class="text-break">Position</th>
                                    <th class="text-break">Credibility</th>
                                    <th class="text-break">Platform</th>
                                    <th class="text-break">Image</th>
                                    <th class="text-break rounded-end">Action</th>
                                </tr>
                            </thead>
                            <tbody id="candidates-search">
                                <?php
                                $query = "SELECT c.candidate_id, c.firstname, c.middleInitial, c.lastName, 
                                c.gender,p.position_id , p.position_name, c.credibilities, c.platform, c.avatar
                                FROM candidates c
                                JOIN positions p ON c.position_id = p.position_id
                                ORDER BY c.candidate_id ASC";
                                $result = $conn->query($query);

                                if ($result->num_rows > 0) {
                                    while ($candidate = $result->fetch_assoc()) { ?>
                                        <tr class="text-center" id="tr">
                                            <td class="rounded-start p-2"><?php echo $candidate['candidate_id'] ?></td>

                                            <td><?php echo htmlspecialchars($candidate['firstname']) . " " .
                                                htmlspecialchars($candidate['middleInitial']) . " " .
                                                htmlspecialchars($candidate['lastName']); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($candidate['gender']); ?></td>
                                            <td><?php echo htmlspecialchars($candidate['position_name']); ?></td>
                                            <td class="w-25 text-break">
                                                <?php echo htmlspecialchars($candidate['credibilities']); ?>
                                            </td>
                                            <td class="text-break"><?php echo htmlspecialchars($candidate['platform']); ?>
                                            </td>
                                            <td>
                                                <img src="../assets/userprofiles/<?php echo htmlspecialchars($candidate['avatar']); ?>"
                                                    alt="Avatar" class="rounded-circle" width="40" height="40">
                                            </td>
                                            <td class="rounded-end">
                                                <a class="editbtn btn btn-success btn-sm"
                                                    data-id="<?php echo $candidate['candidate_id']; ?>"
                                                    data-fname="<?php echo $candidate['firstname']; ?>"
                                                    data-mi="<?php echo $candidate['middleInitial']; ?>"
                                                    data-lname="<?php echo $candidate['lastName']; ?>"
                                                    data-gender="<?php echo $candidate['gender']; ?>"
                                                    data-position="<?php echo $candidate['position_id']; ?>"
                                                    data-credibilities="<?php echo $candidate['credibilities']; ?>"
                                                    data-platform="<?php echo $candidate['platform']; ?>"
                                                    data-avatar="<?php echo $candidate['avatar']; ?>">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a class="deleteCandidate btn btn-danger btn-sm"
                                                    data-id="<?php echo $candidate['candidate_id']; ?>">
                                                    <i class="bi bi-trash-fill"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center text-muted'>No candidates available.</td></tr>";
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
    <?php
    $conn->close();
    ?>
    <!-- Bootstrap Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="adminScript.js"></script>
    <!-- jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- AJAX script -->
    <script>
        $(document).ready(function () {
            $("#searchInput").on("keyup", function () {
                var searchValue = $(this).val();
                if (searchValue != "") {
                    $.ajax({
                        url: "AdminAction/searchCandidate.php",
                        method: "POST",
                        data: { search: searchValue },
                        success: function (response) {
                            $("#candidates-search").html(response);
                        }
                    });
                } else {
                    // Show all candidates
                    $.ajax({
                        url: "AdminAction/reloadCandidatePage.php",
                        method: "POST",
                        success: function (response) {
                            $("#candidates-search").html(response);
                        }
                    });
                }

            });
            //open add modal
            $('#AddCandidate').click(function () {
                $('#AddModal').css('top', '0px');
            });
            //close add modal
            $('#cancelAddCandidate').click(function () {
                $('#AddModal').css('top', '-900px');
            });
            //open edit modal
            $('.editbtn').click(function () {

                var candidateID = $(this).data('id');
                var firstName = $(this).data('fname');
                var middleInitial = $(this).data('mi');
                var lastName = $(this).data('lname');
                var gender = $(this).data('gender');
                var position = $(this).data('position');
                var credibilities = $(this).data('credibilities');
                var platform = $(this).data('platform');
                var avatar = $(this).data('avatar');
                $('#EditModal').find('#candidateID').val(candidateID);
                $('#EditModal').find('#newFirstName').val(firstName);
                $('#EditModal').find('#newMiddleInitial').val(middleInitial);
                $('#EditModal').find('#newLastName').val(lastName);
                $('#EditModal').find('#newGenderSelect').val(gender);
                $('#EditModal').find('#newSelectedPosition').val(position);
                $('#EditModal').find('#newCredibility').val(credibilities);
                $('#EditModal').find('#newPlatform').val(platform);
                $('#EditModal').find('#newImage').val(avatar);

            });
            $(document).on("click", ".editbtn", function () {
                let candidateID = $(this).data("id");
                let fname = $(this).data("fname");
                let mi = $(this).data("mi");
                let lname = $(this).data("lname");
                let gender = $(this).data("gender");
                let position = $(this).data("position");
                let credibilities = $(this).data("credibilities");
                let platform = $(this).data("platform");
                let avatar = $(this).data("avatar");

                // Fill modal inputs
                $("#candidateID").val(candidateID);
                $("#newFirstName").val(fname);
                $("#newMiddleInitial").val(mi);
                $("#newLastName").val(lname);
                $("#newGenderSelect").val(gender);
                $("#newSelectedPosition").val(position);
                $("#newCredibility").val(credibilities);
                $("#newPlatform").val(platform);

                // Show modal
                $('#EditModal').css('top', '0px');
            });
             //close edit modal
             $('#cancelEditCandidate').click(function () {
                $('#EditModal').css('top', '-900px');
            });
            //delete candidate
            $(document).on("click", ".deleteCandidate", function () {
                let candidateID = $(this).data("id");
                $("#del-id").val(candidateID);
                $("#delete-candidate").css('top', '0px');
            });
            $('#cancel-delete-btn').click(function() {
                $("#delete-candidate").css('top', '-900px');
                $("#del-id").val(candidateID).val("");
            })
           
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

        // Toggle icon

    }
    //handle logout
    function Logout() {
        window.location.href = "/Byte.net/vote/logout.php";
    }
</script>

</html>