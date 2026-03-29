<?php
session_start();
$_SESSION['user'] = $dbName;  
// DB Connection
$conn = new mysqli("localhost", "root", "", "users");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Run only on POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get & sanitize input
    $college_code = strtolower(trim($_POST["collegecode"] ?? ""));
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    // Validate inputs
    if (empty($email) || empty($password) || empty($college_code)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: logi.html");
        exit();
    }

    // Prepare query (ONLY email here for flexibility)
    $stmt = $conn->prepare(
        "SELECT name, email, password, role, college_code 
         FROM login 
         WHERE email = ?"
    );

    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check user exists
    if ($stmt->num_rows > 0) {

        $stmt->bind_result($dbName,$dbEmail, $dbPassword, $role, $dbCollege);
        $stmt->fetch();

        // ✅ Check college code (case-insensitive)
        if (strtolower(trim($dbCollege)) !== $college_code) {
            $_SESSION['error'] = "Invalid college code.";
            header("Location: logi.html");
            exit();
        }

        // ✅ Verify hashed password
        if (password_verify($password, $dbPassword)) {

            // Store session
            $_SESSION['user'] = $dbName;
            $_SESSION['email']=$dbEmail;
            $_SESSION['role'] = $role;
            $_SESSION['college_code'] = $dbCollege;

            // Redirect
            header("Location: interface.php");
            exit();

        } else {
            $_SESSION['error'] = "Incorrect password.";
        }

    } else {
        $_SESSION['error'] = "User not found.";
    }

    // Cleanup
    $stmt->close();
    $conn->close();

    // Redirect back
    header("Location: logi.html");
    exit();
}
?>