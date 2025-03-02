<?php
    
    $data = [];
    for($i = 0; $i<4; $i++){
        $randomnum = rand(1, 100);
        array_push($data, $randomnum);
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BYTEVote</title>
    <!-- icon -->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="../indexStyle.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap Components -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/5d9167517f.js" crossorigin="anonymous"></script>
    
</head>
<body >
    <header class="header-container container-fluid d-flex flex-row justify-content-between">
        <div class=" d-flex flex-column w-50 justify-content-center align-items-center">
            <img src="../assets/byte-icon.png" alt="Byte.net" class="byte-icon">
            <p class="upper-text text-light w-75 text-center ">Brackets of Young Technologist Escalating Through Network</p>
        </div>
        <div class=" d-flex  flex-row justify-content-end align-items-center gap-4">
            <img src="../assets/loyola-shs-logo.png" alt="loyola-shs-logo" class="loyola-shs-logo logo">
            <img src="../assets/Byte-net-logo.png" alt="" class="byte-net-logo logo">
        </div>
    </header>
    
        <div class="content-container">
            <div class="content-wrapper">
                <div class="main-content">
                    <h3 class="byteVote-text">THANK YOU FOR YOUR PARTICIPATION</h3>
                    <p class=" text1 w-100 text-center text-wrap">To see the final result visit our facebook page.</p>
                    <button class="btn btn-light" onclick="GoToLogin()"><i class="bi bi-facebook p-2 fs-4 text-primary"></i> BYTEVote</button>
                </div>
                <div class="trojan-img">
                    <img src="../assets/trojan-ICT.png" alt="trojan-ICT" id="trojan-ICT">
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap Scripts -->
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="indexScript.js"></script>
    
</body>
<script>
    function GoToLogin() {

        window.location.href = "logout.php";
    }

</script>
</html>
