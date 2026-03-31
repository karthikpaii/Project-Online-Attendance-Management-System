<?php
session_start();

$conn = new mysqli("localhost", "root", "", "users");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $rollno = trim($_POST['roll']);
    $dob = trim($_POST['password']);

    if (empty($rollno) || empty($dob)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: student.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT name, rollno, dob, batch_name, class_name, college_code FROM students WHERE rollno = ?");
    $stmt->bind_param("s", $rollno);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {

        $stmt->bind_result($dbName, $dbRoll, $dbDob, $dbBatch, $dbClass, $dbCollege);
        $stmt->fetch();

        
        if (date("Y-m", strtotime($dbDob)) === date("Y-m", strtotime($dob))) {

            $_SESSION['user'] = $dbName;
            $_SESSION['batch'] = $dbBatch;
            $_SESSION['class'] = $dbClass;
            $_SESSION['roll'] = $dbRoll;
            $_SESSION['college_code'] = $dbCollege;

            header("Location: inter.php"); 
            exit();

        } else {
            $_SESSION['error'] = "Incorrect Date of Birth.";
        }

    } else {
        $_SESSION['error'] = "User not found.";
    }

    $stmt->close();
    $conn->close();

    header("Location: student.php");
    exit();
}
?>