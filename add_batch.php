<?php  
session_start();

if (!isset($_SESSION['user']) || !isset($_SESSION['college_code'])) {
    header("Location: sign.html");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $batch_name = trim($_POST["batch_name"]);
    $classes = trim($_POST["classes"]);
    $year = trim($_POST["year"]);
    $college_code = $_SESSION['college_code'];

   
    if (empty($batch_name) || empty($classes) || empty($year)) {
        $_SESSION['error'] = "All fields are required!";
    } else {

      
        $conn = new mysqli("localhost", "root", "", "users");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        
        $stmt = $conn->prepare("INSERT INTO batches (batch_name, classes, year, college_code) VALUES (?, ?, ?, ?)");

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        
        $stmt->bind_param("ssss", $batch_name, $classes, $year, $college_code);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Batch added successfully!";
        } else {
            $_SESSION['error'] = "Error adding batch!";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Batch</title>
</head>
<body>

<h2>Add New Batch</h2>

<?php
if (isset($_SESSION['success'])) {
    echo "<p style='color:green'>" . $_SESSION['success'] . "</p>";
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo "<p style='color:red'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']);
}
?>

<!-- ✅ Form -->
<form method="POST" action="add_batch.php">

    <label>Batch Name:</label><br>
    <input type="text" name="batch_name" required><br><br>

    <label>Class:</label><br>
    <input type="text" name="classes" placeholder="e.g. BCA, BCOM" required><br><br>

    <label>Year:</label><br>
    <input type="text" name="year" placeholder="e.g. 2026" required><br><br>

    <button type="submit">Add Batch</button>

</form>

<br>
</body>
</html>