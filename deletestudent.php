
<?php
session_start();
$conn = new mysqli("localhost", "root", "", "users");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the student ID from the URL
if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']); 

    // Delete the student from the database
    $sql = "DELETE FROM student WHERE id = '$id'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Student deleted successfully.');
                window.location.href = 'interface.php'; // Redirect to your dashboard or student view
              </script>";
    } else {
        echo "<script>
                alert('Error deleting student: " . $conn->error . "');
                window.history.back();
              </script>";
    }
} else {
    echo "<script>
            alert('No student ID specified.');
            window.history.back();
          </script>";
}

$conn->close();
?>
