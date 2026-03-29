<?php
session_start();

// DB Connection
$conn = new mysqli("localhost", "root", "", "users");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email'] ?? '');
    $dob   = trim($_POST['password'] ?? ''); // password field contains DOB

    if (empty($email) || empty($dob)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: student.html");
        exit();
    }

    // Get student by email
    $stmt = $conn->prepare("SELECT name, email, dob, college_code FROM students WHERE email = ?");
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {

        $stmt->bind_result($dbName, $dbEmail, $dbDob, $dbCollege);
        $stmt->fetch();

        // Compare input DOB with DB DOB
        if ($dob === $dbDob) {
            $_SESSION['user'] = $dbName;
            $_SESSION['email'] = $dbEmail;
            $_SESSION['dob'] = $dbDob;
            $_SESSION['college_code'] = $dbCollege; // fetched automatically

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