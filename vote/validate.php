<?php
// Enable error reporting for debugging (remove in production)
session_start();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Database connection settings
    $host   = 'localhost';
    $db     = 'db_votingsystem';
    $dbUser = 'root';
    $dbPass = '1101';

    // Create Data Source Name (DSN)
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Disable emulated prepared statements
    ];

    $error = "";

    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass, $options);

        // Retrieve and sanitize form data
        // (If you are sending JSON, replace this block with JSON decoding.)
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        $lrn = trim($_POST['lrn'] ?? '');
        $gender = trim($_POST['gender'] ?? '');
        $preferred_username = trim($_POST['preffered_username'] ?? ''); // Check your field names!
        $preferred_password = $_POST['preffered_password'] ?? '';

        //check if the gender and select a random profile picture
        // Determine profile picture based on gender selection
        switch ($gender) {
            case 'M':
                $profilePicture = ['default_profile-m1.jpg', 'default_profile-m2.jpg'];
                break;
            case 'F':
                $profilePicture = ['default_profile-f1.jpg', 'default_profile-f2.jpg'];
                break;
            case 'Other':
            case 'prefer not to say':
                $profilePicture = ['default_profile-m1.jpg', 'default_profile-m2.jpg', 'default_profile-f1.jpg', 'default_profile-f2.jpg'];
                break;
        }

        // Select a random profile picture
        $defProfile = $profilePicture[array_rand($profilePicture)];
        

        // Validate the LRN format (must be exactly 9 digits)
        if (!preg_match('/^\d{12}$/', $lrn)) {
            $_SESSION['popup'] = "Invalid LRN.";
            header("Location: register.php");
            
            exit;
        }

        // Basic validation: ensure required fields are not empty
        if (empty($firstname) || empty($lastname) || empty($lrn) || empty($preferred_username) || empty($preferred_password) || empty($gender)) {
            $error = "All fields are required.";
            throw new Exception($error);
        }

        // Check if the username already exists
        $stmt = $pdo->prepare("SELECT * FROM voters WHERE username = :username");
        $stmt->execute([':username' => $preferred_username]);
        if ($stmt->fetch()) {
            $_SESSION['popup'] = '<div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill p-1 fs-4"></i>
                <div>Username already exist. please try another.</div>
            </div>';
            header("Location: register.php");
            exit;
        }

        // Hash the password
        $hashedPassword = password_hash($preferred_password, PASSWORD_DEFAULT);

        // Prepare the INSERT query
        $sql = "INSERT INTO voters (firstname, lastname, gender, image_path, lrn, username, password) 
                VALUES (:firstname, :lastname, :gender, :image_path, :lrn, :username, :password)";
        $stmt = $pdo->prepare($sql);

        // Execute the query and check for errors
        if (!$stmt->execute([
            ':firstname' => $firstname,
            ':lastname'  => $lastname,
            ':gender' => $gender,
            ':image_path' => $defProfile,  // Randomly select a profile picture
            ':lrn'       => $lrn,
            ':username'  => $preferred_username,
            ':password'  => $hashedPassword,
        ])) {
            // If execution fails, grab error info
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Insert failed: " . $errorInfo[2]);
        }

        // If insert is successful, output a success message with JavaScript redirect
        $_SESSION['popup'] = '<div class="alert alert-success d-flex align-items-center" role="alert">
        <i class="bi bi-check-circle-fill p-2 fs-5"></i>
        <div>Register Successfully.</div>
        </div>';
        $_SESSION['script'] = '
        setTimeout(() => {
           window.location.href= "login.php";
            setTimeout(() => notif.remove(), 300); // Remove from DOM after animation
        }, 3000);
        ';
        header("Location: register.php");
        exit;
    } catch (Exception $e) {
        // Log the error for debugging (in production, avoid showing details)
        error_log("Registration Error: " . $e->getMessage());

        // Output an error message and redirect
        $_SESSION['popup'] = "LRN already exists. Please check your LRN";
        header("Location: register.php");
        exit;
    }
}
?>

