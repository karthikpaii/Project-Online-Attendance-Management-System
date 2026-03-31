<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    $username = trim($_POST["Name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $date = trim($_POST["bdate"]);
    $college_code = trim($_POST["college_code"]);

   
    $role = "admin";

    // validation
    if (empty($username) || empty($email) || empty($password) || empty($date) || empty($college_code)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: sign.html");
        exit();
    }

    // DB connection
    $conn = new mysqli("localhost", "root", "", "users");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // check if email exists
    $stmt = $conn->prepare("SELECT email FROM login WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {

        echo "<script>
            alert(' Email Already Registered! ');
            window.location.href = 'sign.html';
        </script>";
        exit();

    }

    $stmt->close();

    // hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // insert user with college_code
    $stmt = $conn->prepare("INSERT INTO login (name, email, password, bdate, role, college_code) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $email, $hashed_password, $date, $role, $college_code);

    if ($stmt->execute()) {

        // store in session
        $_SESSION['user'] = $username;
        $_SESSION['role'] = $role;
        $_SESSION['college_code'] = $college_code;

        // redirect (admin only)
        header("Location: interface.php");
        exit();

    } else {
        $_SESSION['error'] = "Something went wrong. Try again.";
        header("Location: sign.html");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>