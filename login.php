<?php
session_start();

// DB connection
$conn = new mysqli("localhost", "root", "", "users");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $college_code = strtolower(trim($_POST["collegecode"]));
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Empty check
    if (empty($college_code) || empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: logi.php");
        exit();
    }

    // Query
    $stmt = $conn->prepare("SELECT name, email, password, role, college_code FROM login WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // User exists
    if ($stmt->num_rows > 0) {

        $stmt->bind_result($name, $dbEmail, $dbPassword, $role, $dbCollege);
        $stmt->fetch();

        // Check college code
        if (strtolower(trim($dbCollege)) !== $college_code) {
            $_SESSION['error'] = "Invalid college code.";
            header("Location: logi.php");
            exit();
        }

        // Check password
        if (password_verify($password, $dbPassword)) {

            $_SESSION['user'] = $name;
            $_SESSION['email'] = $dbEmail;
            $_SESSION['role'] = $role;
            $_SESSION['college_code'] = $dbCollege;

            // ✅ SUCCESS REDIRECT
            header("Location: interface.php");
            exit();

        } else {
            $_SESSION['error'] = "Incorrect password.";
        }

    } else {
        $_SESSION['error'] = "User not found.";
    }

    $stmt->close();
    $conn->close();

    header("Location: logi.php");
    exit();
}
?>