<?php
session_start();

// DB connection
$conn = new mysqli("localhost", "root", "", "users");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only run when form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $rollno = trim($_POST['roll']);
    $dob = trim($_POST['password']);

    // Check empty
    if (empty($rollno) || empty($dob)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: student.html");
        exit();
    }

    // Prepare query
    $stmt = $conn->prepare("SELECT name, rollno, dob, college_code FROM students WHERE rollno = ?");
    $stmt->bind_param("s", $rollno);
    $stmt->execute();
    $stmt->store_result();

    // Check user exists
    if ($stmt->num_rows > 0) {

        $stmt->bind_result($dbName, $dbRoll, $dbDob, $dbCollege);
        $stmt->fetch();

        // Compare DOB (safe comparison)
       if (substr($dbDob, 0, 10) === $dob) {

    $_SESSION['user'] = $dbName;
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

    header("Location: student.html");
    exit();
}
?>