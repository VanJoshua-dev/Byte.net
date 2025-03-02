<?php
session_start(); // Start session to access session variables

// Check if there's an error message
if (isset($_SESSION["error"])) {
    echo '<div class="w-100 d-flex justify-content-center align-items-center pt-1 position-fixed z-3 top-0 animate__animated animate__fadeIn" id="failed-notif">
    <div class="alert alert-danger d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill p-1 fs-4"></i>
        <div>'.$_SESSION["error"].'</div>
    </div>
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
    <title>Login</title>
    <!-- icon -->
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="Styles/loginStyle.css">
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
            <h1 class="text-center">BYTEVote Login</h1>
            <form action="loginValidation.php" method="POST" id="login-form" class="d-flex flex-column justify-content-center align-items-center h-75">
                <div class="mb-3 username-wrapper">
                    <label for="username" class="form-label ">Username</label>
                    <input type="text" class="form-control w-100" name="username" id="username" required>
                </div>
                <div class="mb-3 password-wrapper">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control w-100" name="password" id="password" required>
                    <div class="d-flex flex-row justify-content-around">
                        <div class="form-check w-auto">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">

                            <label class="form-check-label" for="flexCheckDefault">
                                Show password
                            </label>
                        </div>
                        <a href="#" class="forgot-password">Forgot Password?</a>
                    </div>
                    
                </div>
                
                <button type="submit" class="bg-primary p-3 mt-2 border border-2 border-transparent rounded-5">Login</button>
                 <p class="mt-2">Don't have an account? <a href="register.php">Sign up here</a></p>
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
    <script type="text/javascript" src="Scripts/loginScript.js"></script>
</body>
<script>
    document.querySelector("#flexCheckDefault").addEventListener("change", function() {
        var passwordInput = document.querySelector("#password");
        if(this.checked){
            passwordInput.type = "text";
        }
        else{
            passwordInput.type = "password";
        }
    })
    document.addEventListener("DOMContentLoaded", function () {
        console.log("Script loaded!");
    });
    // document.querySelector("#login-form").addEventListener("submit", function (event){
    //     event.preventDefault();
    // });
    
</script>

</html>