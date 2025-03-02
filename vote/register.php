<?php
session_start();
if (isset($_SESSION['popup']) && empty($_SESSION["script"])) {
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
} else if (isset($_SESSION['popup']) && $_SESSION["script"]) {
    echo '<div class="w-100 d-flex justify-content-center align-items-center pt-1 position-fixed z-3 top-0 animate__animated animate__fadeIn" id="failed-notif">' .
        $_SESSION['popup']
        . '</div>
        <script>' . $_SESSION["script"] . '
            setTimeout(() => {
                let notif = document.querySelector("#failed-notif");
                notif.style.opacity = "0";
                notif.style.transform = "translateY(-50px)";
                setTimeout(() => notif.remove(), 300); // Remove from DOM after animation
            }, 2000);
            
        </script>';
    unset($_SESSION["popup"]);
    unset($_SESSION["script"]);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- icon -->
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="Styles/registerStyle.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap Components -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- font styles -->
    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/5d9167517f.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="login-container container-fluid ">
        <div class="form-container border border-2 border-0 rounded-2 container">
            <h1 class="text-center">BYTEVote Register</h1>
            <form action="validate.php" method="POST" id="login-form"
                class="d-flex flex-column justify-content-center align-items-center mt-4">
                <div class="mb-3 username-wrapper">
                    <label for="firstname" class="form-label ">Firstname</label>
                    <input type="text" class="form-control w-100" name="firstname" id="username"
                        placeholder="Ex. John Doe" required>
                </div>
                <div class="mb-3 password-wrapper">
                    <label for="lastname" class="form-label">Lastname</label>
                    <input type="text" class="form-control w-100" name="lastname" id="lastname"
                        placeholder="Ex. Dela Cruz" required>
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
                <div class="lrn-wrapper">
                    <label for="lrn" class="form-label">LRN</label>
                    <input type="number" class="form-control w-100" name="lrn" id="lrn" placeholder="LRN" required>
                </div>
                <div class="preffered-username">
                    <label for="preffered_username" class="form-label">Preferred Username</label>
                    <input type="text" class="form-control w-100" name="preffered_username" id="preffered_username"
                        placeholder="Preferred Username" required>
                </div>
                <div class="preffered-password">
                    <div class="">
                        <label for="preffered_password" class="form-label">Preferred Password</label>
                        <input type="password" class="form-control w-100" name="preffered_password"
                            id="preffered_password" placeholder="Preferred Password" required>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                            Show Password
                        </label>
                    </div>

                </div>
                <button type="submit"
                    class="bg-primary p-3 mt-3 border border-2 border-transparent rounded-5">Register</button>
                <p class="text-center mt-3">Already have an account? <a href="login.php">Login</a></p>
            </form>
        </div>
        <div class="web-logo d-flex flex-column justify-content-start gap-4">
            <div class="img-wrapper-1 d-flex flex-row justify-content-end gap-3">
                <img src="../assets/loyola-shs-logo.png" alt="loyola logo" class="loyola logo">
                <img src="../assets/Byte-net-logo.png" alt="ByteVote logo" class="byte-net logo">
            </div>
            <div class="img-wrapper-2 d-flex justify-content-end">
                <img src="../assets/trojan-ICT.png" alt="trojan-ICT logo" class="trojan-ICT logo">
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="Scripts/registerScript.js"></script>
</body>
<script>
    // Show/Hide Password
    document.querySelector("#flexCheckDefault").addEventListener("change", function () {
        var password = document.querySelector("#preffered_password");
        if (this.checked) {
            password.type = "text";
        } else {
            password.type = "password";
        }
    })


    //fetch from php
    // Show/Hide Password on blur event
    //    function triggerPopup(){
    //     if(user != "" && pass != ""){
    //         if(user == usern && passw == pass){
    //         setTimeout(() => {
    //             document.querySelector(".popup-success").style.top = '-40px';
    //             document.querySelector("#preffered_username").value = "";
    //             document.querySelector("#preffered_password").value = "";
    //         }, 2000);
    //         document.querySelector(".popup-success").style.top = '5px';
    //         }
    //          else if(user != usern && passw != pass){
    //         setTimeout(() => {
    //             document.querySelector(".popup-failed").style.top = '-70px';
    //             document.querySelector("#preffered_username").value = "";
    //             document.querySelector("#preffered_password").value = "";
    //         }, 2000);
    //         document.querySelector(".popup-failed").style.top = '5px';
    //     }
    //     }
    // }
</script>

</html>