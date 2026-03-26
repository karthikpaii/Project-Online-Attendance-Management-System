<!--Files are view_student_form.php, view_student.php, delete_student.php, editstudent.php, deletesidebar.php, updatesidebar.php--->
<style>
    h2
    {
        text-align:center;
    }
      table {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px #333;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
          
        }

        table th, table td, table tr {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ccc;
            
        }


        table th {
            background-color: #f7f7f7;
        }

        table tr:hover {
            background-color:rgb(196, 186, 186);
        }
    </style>

<?php
session_start();
$conn = new mysqli("localhost", "root", "", "users");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$batch = isset($_GET['batch']) ? $_GET['batch'] : '';
$class = isset($_GET['class']) ? $_GET['class'] : '';

$sql = "SELECT * FROM students WHERE 1";
if (!empty($batch)) {
    $sql .= " AND batch_name = '" . $conn->real_escape_string($batch) . "'";
}
if (!empty($class)) {
    $sql .= " AND class_name = '" . $conn->real_escape_string($class) . "'"; //.= allows you to append only the parts needed without rewriting the whole query.
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Student List:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Roll No</th><th>Name</th><th>Batch</th><th>Class</th><th>Phone</th><th>Email</th><th>Edit</th><th>Delete</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['rollno']) . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['batch_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['class_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        $id = urlencode($row['id']);
        echo "<td><a href='editstudent.php?id=$id'>Edit</a></td>";
        echo "<td><a href='deletestudent.php?id=$id' onclick=\"return confirm('Are you sure you want to delete this student?');\">Delete</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No students found for selected batch and class.</p>";
}

$conn->close();
?>
