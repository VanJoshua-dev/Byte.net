<?php
session_start();
$conn = new mysqli("localhost", "root", "1101", "db_votingsystem");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $candidateID = $_POST["candidateID"];
    $newFirstName = trim($_POST["newFirstName"]);
    $newMiddleInitial = trim($_POST["newMiddleInitial"]);
    $newLastName = trim($_POST["newLastName"]);
    $newGender = trim($_POST["newGenderSelect"]);
    $newPosition = $_POST["newSelectedPosition"];
    $newCredibility = trim($_POST["newCredibility"]);
    $newPlatform = trim($_POST["newPlatform"]);

    // Fetch existing image
    $query = "SELECT avatar FROM candidates WHERE candidate_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $candidateID);
    $stmt->execute();
    $stmt->bind_result($currentImage);
    $stmt->fetch();
    $stmt->close();

    $image = $currentImage; // Keep existing image by default

    // Allowed file extensions
    $allowedExtensions = ["jpg", "jpeg", "png"];

    if (!empty($_FILES["newImage"]["name"])) {
        $targetDir = "../assets/userprofiles/"; // Ensure this folder exists
        $fileName = $_FILES["newImage"]["name"];
        $fileTmpName = $_FILES["newImage"]["tmp_name"];
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validate file extension
        if (in_array($fileType, $allowedExtensions)) {
            // Generate a unique file name
            $uniqueFileName = uniqid() . "_" . time() . "." . $fileType;
            $targetFilePath = $targetDir . $uniqueFileName;

            // Ensure the directory exists
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Move the uploaded file
            if (move_uploaded_file($fileTmpName, $targetFilePath)) {
                $image = $uniqueFileName; // Update image path
            } else {
                $_SESSION["error"] = "<div class='alert alert-danger d-flex align-items-center' role='alert'>
                <i class='bi bi-exclamation-triangle-fill text-danger p-2'></i>
                <div>Upload Failed!</div>
            </div>";
            }
        } else {
            $_SESSION["error"] = "<div class='alert alert-danger d-flex align-items-center' role='alert'>
                <i class='bi bi-exclamation-triangle-fill text-danger p-2'></i>
                <div>Upload Failed!</div>
            </div>";
            header("Location: ../Candidates.php");
            exit();
        }
    }

    // Update the candidate record
    $updateQuery = "UPDATE candidates 
                    SET firstname=?, middleInitial=?, lastName=?, gender=?, position_id=?, credibilities=?, platform=?, avatar=? 
                    WHERE candidate_id=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssisssi", $newFirstName, $newMiddleInitial, $newLastName, $newGender, $newPosition, $newCredibility, $newPlatform, $image, $candidateID);

    if ($stmt->execute()) {
        $_SESSION["error"] = "<div class='alert alert-success d-flex align-items-center' role='alert'>
        <i class='bi bi-check-circle-fill text-success p-2'></i>
        <div>Update Successfully!</div>
    </div>";
        header("Location: ../Candidates.php");
        exit();
    } else {
        $_SESSION["error"] = "<div class='alert alert-danger d-flex align-items-center' role='alert'>
        <i class='bi bi-exclamation-triangle-fill text-danger p-2'></i>
        <div>Update Failed!</div>
    </div>";
        header("Location: ../Candidates.php");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>