<?php
session_start();

// ✅ CHECK LOGIN
if (!isset($_SESSION['user'])) {
    header("Location: student.php");
    exit();
}

// ✅ GET SESSION DATA
$college_code = $_SESSION['college_code'];
$name = $_SESSION['user'];
$roll = $_SESSION['roll'];
$batch = $_SESSION['batch'];
$class = $_SESSION['class'];

// ✅ DB CONNECTION
$conn = new mysqli("localhost", "root", "", "users");

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// ✅ PREPARE QUERY
$stmt = $conn->prepare("SELECT student_roll, student_name, batch_name,class_name, subject, date, status FROM attendance WHERE student_roll=? AND college_code=?");
$stmt->bind_param("ss", $roll, $college_code);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        .info {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background: #2c3e50;
            color: white;
        }

        tr:hover {
            background: #f1f1f1;
        }
    </style>
</head>

<body>

<h2>Student Attendance</h2>

<div class="info">
    <strong>Name:</strong> <?php echo htmlspecialchars($name); ?> |
    <strong>Roll:</strong> <?php echo htmlspecialchars($roll); ?> |
    <strong>Batch:</strong> <?php echo htmlspecialchars($batch); ?> |
    <strong>Class:</strong> <?php echo htmlspecialchars($class); ?> |
    
</div>

<?php
if ($result->num_rows > 0) {

    echo '<table>
            <tr>
                <th>Roll No</th>
                <th>Name</th>
                <th>Subject</th>
                <th>Date</th>
                <th>Status</th>
            </tr>';

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['student_roll']) . "</td>
                <td>" . htmlspecialchars($row['student_name']) . "</td>
                <td>" . htmlspecialchars($row['subject']) . "</td>
                <td>" . htmlspecialchars($row['date']) . "</td>
                <td>" . htmlspecialchars($row['status']) . "</td>
              </tr>";
    }

    echo '</table>';

} else {
    echo "<p style='text-align:center;'>No attendance records found.</p>";
}

// ✅ CLOSE
$stmt->close();
$conn->close();
?>
</script>
</body>
</html>