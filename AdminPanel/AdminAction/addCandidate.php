<?php
session_start();
$conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST["firstName"]);
    $middleInitial = trim($_POST["middleInitial"]);
    $lastName = trim($_POST["lastName"]);
    $gender = trim($_POST["gender"]);
    $selectedPosition = $_POST["selectedPosition"];
    $credibility = trim($_POST["credibility"]);
    $platform = trim($_POST["platform"]);
    
    $image = ""; // Initialize image variable

    // Allowed file extensions
    $allowedExtensions = ["jpg", "jpeg", "png"];
    $targetDir = "../../assets/userprofiles/"; // Ensure this folder exists

    // Handle file upload
    if (!empty($_FILES["image"]["name"])) {
        $fileName = $_FILES["image"]["name"];
        $fileTmpName = $_FILES["image"]["tmp_name"];
        $fileSize = $_FILES["image"]["size"];
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); // Get file extension

        // Validate file extension
        if (!in_array($fileType, $allowedExtensions)) {
            echo "Invalid file type! Only JPG, JPEG, and PNG files are allowed.";
            exit;
        }
        
        // Ensure the target directory exists
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Generate a unique file name
        $uniqueFileName = uniqid("candidate_", true) . "." . $fileType;
        $targetFilePath = $targetDir . $uniqueFileName;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($fileTmpName, $targetFilePath)) {
            $image = $uniqueFileName; // Store unique filename
        } else {
            echo "Error uploading file.";
            exit;
        }
    } else {
        //Assign a default profile picture based on gender
        $defaultImages = [
            "M" => ["default_profile-m1.jpg", "default_profile-m2.jpg"],
            "F" => ["default_profile-f1.jpg", "default_profile-f2.jpg"],
            "Other" => ["default_profile-m1.jpg", "default_profile-m2.jpg", "default_profile-f1.jpg", "default_profile-f2.jpg"],
            "Prefer not to say" => ["default_profile-m1.jpg", "default_profile-m2.jpg", "default_profile-f1.jpg", "default_profile-f2.jpg"]
        ];

        // Select a random default profile picture
        $image = $defaultImages[$gender][array_rand($defaultImages[$gender])];
    }

    //Insert data into the `candidates` table (including gender)
    $stmt = $conn->prepare("INSERT INTO candidates (firstname, middleInitial, lastName, gender, position_id, credibilities, platform, avatar) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssisss", $firstName, $middleInitial, $lastName, $gender, $selectedPosition, $credibility, $platform, $image);

    if ($stmt->execute()) {
        echo "Candidate added successfully!";
        header("Location:../Candidates.php");
    } else {
        echo "Error: " . $stmt->error;
        header("Location:../Candidates.php");
    }

    $stmt->close();
}

$conn->close();
?>
