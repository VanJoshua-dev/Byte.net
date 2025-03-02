<?php
session_start();
if (!isset($_SESSION["voter_id"]) || $_SESSION["role"] !== "voter") {
    header("Location: /Byte.net/vote/login.php");
    exit();
}
$avatar = $_SESSION["image_path"];
$firstname = $_SESSION["firstname"];
$lastname = $_SESSION["lastname"];
$lrn = $_SESSION['lrn'];
$role = $_SESSION["role"];

$conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$voter_id = $_SESSION['voter_id'];

// Check if the voter has already voted
$hasVoted = false;
$query = "SELECT voted FROM voters WHERE voter_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $voter_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $hasVoted = $row['voted'] == 1;
}
$stmt->close();

// If voter has already voted, prevent access
if ($hasVoted) {
    echo "<script>
        window.location.href = 'thank_you.php';
    </script>";
    exit();
}
// Check if an active election exists
$query = "SELECT * FROM elections WHERE is_active = 1 LIMIT 1";
$result = $conn->query($query);
$election = $result->fetch_assoc();

// Fetch candidates only if an election is active
$positions = [];
if ($election) {
    $sql = "SELECT p.position_id, p.position_name, c.candidate_id, c.firstname, c.middleInitial, c.lastName, c.credibilities, c.platform, c.avatar, c.gender 
            FROM positions p 
            LEFT JOIN candidates c ON p.position_id = c.position_id
            ORDER BY p.position_id, c.candidate_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $positions[$row['position_name']][] = $row;
        }
    }
}
if (!$election) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('#voteForm').style.display = 'none';
            document.querySelector('#noElection').innerHTML = 'No election available';
        });
    </script>";
}

$conn->close();
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
    <link rel="stylesheet" href="Styles/HomeStyle.css">
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
                <img src="../assets/userprofiles/<?php echo $avatar; ?>" alt="Profile" class="profile-pic"
                    onclick="toggleProfileOpen()">
                <div class="user-name ">
                    <span><?php echo $lastname; ?></span>
                    <span><?php echo $role; ?></span>
                </div>

                <button type="button" id="dropdownbtn" onclick="toogleDropdown()" class="border-0 bg-transparent"><i
                        class="fa-solid fa-circle-chevron-down" id="dropdownIcon"></i></button>

            </div>
            <div class="dropdown">
                <button type="button" onclick="toggleProfileOpen()"><i class="fa-solid fa-user-circle"></i>
                    Profile</button>
                <button type="button" onclick="LoginPage()"><i class="fa-solid fa-sign-out-alt"></i> Logout</button>
            </div>
        </header>
        <!-- profile info -->
        <div class="profile-info z-3" id="profile">
            <div class="profile-content">
                <form action="" id="profile-form">

                    <div class="profile-details">
                        <div class="profile-picture gap-1 ">
                            <img src="../assets/userprofiles/<?php echo $avatar; ?>" alt="" class="w-10">
                            <label for="fileUpload" class="upload mt-1 p-1 rounded bg-primary">upload image</label>
                            <input type="file" id="fileUpload" hidden>
                        </div>
                        <div class="profile-class">

                            <div>
                                <h2><span><?php echo $firstname . " " . $lastname; ?></span></h2>
                                <p><span><?php echo $role; ?></span></p>
                            </div>

                        </div>

                    </div>
                    <div class="editable-field ">
                        <div class="username d-flex flex-column">
                            <label for="firstname">Firstname</label>
                            <input type="text" name="firstname" id="firstname" class="w-10"
                                value="<?php echo $firstname . " " . $lastname ?>">
                        </div>
                        <div class="stu-lrn d-flex flex-column">
                            <label for="lrn">LRN</label>
                            <input type="number" name="lrn" id="lrn" class="w-10" value="<?php echo $lrn; ?>">
                        </div>
                    </div>


                    <div class="profile-buttons mt-4">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                        <button type="button" class="btn btn-danger" onclick="toggleProfile()">Discard</button>
                    </div>
                </form>

            </div>

        </div>
        <main class="main-content p-1 ">
            <div class="content-container p-2 d-flex flex-column gap-5">
                <h1 class="w-100 text-center text-light" id="noElection"></h1>
                <form id="voteForm" action="voteFunction.php" method="POST">
                    <?php foreach ($positions as $position => $candidates): ?>
                        <?php if (!empty($candidates)): ?>
                            <div class="content-wrapper mt-4">
                                <h2 class="bg-dark text-light p-2 w-100 rounded text-center"><?= htmlspecialchars($position) ?>
                                </h2>
                                <!-- Hidden input to store selected vote -->
                                <input type="hidden" name="votes[<?= htmlspecialchars($position) ?>]"
                                    id="vote_<?= htmlspecialchars($position) ?>" value="">

                                <?php foreach ($candidates as $candidate): ?>
                                    <div class="card-wrapper">
                                        <?php
                                        $avatarPath = "../assets/userprofiles/" . htmlspecialchars($candidate['avatar']);
                                        if (!file_exists($avatarPath) || empty($candidate['avatar'])) {
                                            $avatarPath = "../assets/default-avatar.png";
                                        }
                                        ?>
                                        <img src="<?= $avatarPath; ?>" alt="Candidate Avatar" id="image">

                                        <div class="d-flex flex-column justify-content-center align-items-center" id="info">
                                            <h2 class="text-center">
                                                <?= htmlspecialchars($candidate['firstname']) . " " .
                                                    (!empty($candidate['middleInitial']) ? htmlspecialchars($candidate['middleInitial']) . " " : "") .
                                                    htmlspecialchars($candidate['lastName']); ?>
                                            </h2>
                                           
                                            <p class="text-center"><?= htmlspecialchars($candidate['credibilities']) ?></p>
                                            <p class="text-center"><?= htmlspecialchars($candidate['platform']) ?></p>

                                            <button type="button" class="btn btn-primary vote-btn" id="vote"
                                                data-position="<?= htmlspecialchars($position) ?>"
                                                data-candidate="<?= htmlspecialchars($candidate['candidate_id']) ?>">
                                                Vote
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success" id="submitbtn">Submit Vote</button>
                    </div>
                </form>
            </div>

        </main>
    </div>


    </div>

    <!-- Bootstrap Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="Scripts/HomeScript.js"></script>
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>

        $("#sideBar").click(function() {

        } )
    </script>

</body>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const voteButtons = document.querySelectorAll(".vote-btn");

        voteButtons.forEach(button => {
            button.addEventListener("click", function () {
                const position = this.getAttribute("data-position");
                const candidateId = this.getAttribute("data-candidate");

                // Store the selected candidate in the hidden input
                document.getElementById("vote_" + position).value = candidateId;

                // Reset all buttons for this position
                document.querySelectorAll(`[data-position='${position}']`).forEach(btn => {
                    btn.classList.remove("btn-success");
                    btn.classList.add("btn-primary");
                });

                // Highlight selected button
                this.classList.remove("btn-primary");
                this.classList.add("btn-success");
            });
        });
    });
    function toggleProfile() {
        console.log('Toggle profile');
        let profile = document.querySelector("#profile"); // Get the sidebar
        profile.style.display = "none"; // Hide it
        console.log('Toggle profile done');
    }
    function toggleProfileOpen() {
        console.log('Toggle profile open');
        let profile = document.querySelector("#profile"); // Get the sidebar
        profile.style.display = "flex"; // Show it
        console.log('Toggle profile open done');
    }
    document.querySelector("#profile-form").addEventListener("submit", function (event) {
        event.preventDefault(); // Prevents the page from reloading
    });


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
    //return to login page
    function LoginPage() {
        window.location.href = "logout.php";
    }
</script>

</html>