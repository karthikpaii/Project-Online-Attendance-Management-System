<!--Back End SingUp--->
<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["Name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $date = trim($_POST["bdate"]);
    $role = trim($_POST["role"]);

    if (empty($username) || empty($email) || empty($password) || empty($date) || empty($role)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: sign.html");
        exit();
    }

    $conn = new mysqli("localhost", "root", "", "users");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT email FROM login WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email); /*binding (connecting) actual PHP variables ($email) to the ? placeholder*/
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>
        alert('🛑 Email Already Registered! ❌');
        window.location.href = 'sign.html';
    </script>";
    exit(); // <-- prevent further PHP from executing
    
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO login (name, email, password, bdate, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $email, $hashed_password, $date, $role);
        if ($stmt->execute()) {
            $_SESSION['user'] = $username;
            $_SESSION['role'] = $role;

            // Redirect based on role
            if ($role === "admin") {
                header("Location: interface.php");
            } else {
                header("Location: inter.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Something went wrong. Try again.";
        }
        /*else {
    die("DB ERROR: " . $stmt->error);
}*/
    }

    $stmt->close();
    $conn->close();

    header("Location: sign.php");
    exit();
}

?>

