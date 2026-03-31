<!DOCTYPE html>
<html>
<head>
<title>Attendance Dashboard</title>

<style>
body {
  font-family: 'Inter', sans-serif;
  background: #f0f2f5;
  margin: 0;
}
h1 {
  text-align: center;
  padding: 20px;
}
.dashboard-container {
  max-width: 1200px;
  margin: auto;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  padding: 20px;
}
.card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
  padding: 25px;
}

.card .details h3 {
  margin: 0;
  font-size: 1rem;
  color: #888;
}
.card .details p {
  font-size: 1.5rem;
  font-weight: bold;
}
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  padding: 40px;
  text-align: left;
}
th {
  background: #4CAF50;
  color: white;
}
tr:nth-child(even) {
  background: #f9f9f9;
}
canvas {
  margin-top: 20px;
  max-width: 100%;
}
.export-btn {
  background: #4CAF50;
  color: white;
  padding: 8px 12px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  margin-top: 10px;
}
</style>

</head>
<body>

<?php 
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);


if (!isset($_SESSION['user']) || $_SESSION['role'] !== "admin") {
    header("Location: sign.html");
    exit();
}

$college_code = $_SESSION['college_code'];

// 🔌 DB CONNECTION
$conn = new mysqli("localhost","root","","users");
if($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$studentsPerClass = $conn->query(
    "SELECT class_name, COUNT(*) AS total_students 
     FROM students 
     WHERE college_code='$college_code'
     GROUP BY class_name"
) or die($conn->error);


$absentPerClassToday = $conn->query(
    "SELECT class_name, COUNT(*) AS absences_today
     FROM attendance
     WHERE status = 'Absent' 
     AND date = CURDATE()
     AND college_code='$college_code'
     GROUP BY class_name"
) or die($conn->error);

$absMap = [];
while($r = $absentPerClassToday->fetch_assoc()) {
    $absMap[$r['class_name']] = $r['absences_today'];
}


$overallStudents = $conn->query(
    "SELECT COUNT(*) AS total 
     FROM students 
     WHERE college_code='$college_code'"
)->fetch_assoc()['total'];

$overallAbsences = $conn->query(
    "SELECT COUNT(*) AS total 
     FROM attendance 
     WHERE status='Absent' 
     AND date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
     AND college_code='$college_code'"
)->fetch_assoc()['total'];


$lowAttendance = $conn->query(
    "SELECT s.rollno, s.name, s.batch_name, s.class_name,
        ROUND(
            (SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) 
            / COUNT(DISTINCT a.date)) * 100, 2
        ) AS percentage
     FROM students s
     JOIN attendance a 
        ON s.rollno = a.student_roll 
        AND s.class_name = a.class_name
     WHERE s.college_code='$college_code'
     GROUP BY s.rollno, s.name, s.batch_name, s.class_name
     HAVING percentage < 85"
) or die($conn->error);

$lowCount = $lowAttendance->num_rows;


$trendQuery = $conn->query(
    "SELECT date, COUNT(*) AS total_absent
     FROM attendance
     WHERE status = 'Absent'
     AND date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
     AND college_code='$college_code'
     GROUP BY date
     ORDER BY date ASC"
) or die($conn->error);

$trendLabels = [];
$trendValues = [];

while($t = $trendQuery->fetch_assoc()) {
    $trendLabels[] = $t['date'];
    $trendValues[] = $t['total_absent'];
}

$topAbsentees = $conn->query(
    "SELECT s.rollno, s.name, s.class_name, COUNT(*) AS absents
     FROM students s
     JOIN attendance a 
        ON s.rollno = a.student_roll
     WHERE a.status='Absent'
     AND s.college_code='$college_code'
     GROUP BY s.rollno, s.name, s.class_name
     ORDER BY absents DESC
     LIMIT 5"
) or die($conn->error);

?>

<h1>Attendance Dashboard</h1>

<div class="dashboard-container">

  <div class="card">
    <div class="details">
      <h3>Total Students</h3>
      <p><?= number_format($overallStudents) ?></p>
    </div>
  </div>

  <div class="card">
    <div class="details">
      <h3>Absences (7 days)</h3>
      <p><?= number_format($overallAbsences) ?></p>
    </div>
  </div>

  <div class="card">
    <div class="details">
      <h3>Students &lt; 85%</h3>
      <p><?= number_format($lowCount) ?></p>
    </div>
  </div>

  <!-- Absences by class -->
  <div class="table-container" style="grid-column: 1 / -1;">
    <h3>Absences Today (By Class)</h3>
    <table>
      <tr>
        <th>Class</th>
        <th>Total Students</th>
        <th>Absences</th>
      </tr>

      <?php while($row = $studentsPerClass->fetch_assoc()):
        $cls = $row['class_name'];
        $tot = $row['total_students'];
        $abs = $absMap[$cls] ?? 0;
      ?>
      <tr>
        <td><?= htmlspecialchars($cls) ?></td>
        <td><?= $tot ?></td>
        <td><?= $abs ?></td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>

  <!-- Top absentees -->
  <div class="table-container">
    <h3>Top 5 Absentees</h3>
    <table>
      <tr>
        <th>Roll No</th>
        <th>Name</th>
        <th>Class</th>
        <th>Total Absents</th>
      </tr>
      <?php while($a = $topAbsentees->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($a['rollno']) ?></td>
        <td><?= htmlspecialchars($a['name']) ?></td>
        <td><?= htmlspecialchars($a['class_name']) ?></td>
        <td><?= $a['absents'] ?></td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>

  <!-- Low attendance -->
  <div class="table-container" style="grid-column: 1 / -1;">
    <h3>Students with &lt; 85% Attendance</h3>

    <table>
      <tr>
        <th>Roll No</th>
        <th>Name</th>
        <th>Class</th>
        <th>Batch</th>
        <th>Attendance %</th>
      </tr>

      <?php while($s = $lowAttendance->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($s['rollno']) ?></td>
        <td><?= htmlspecialchars($s['name']) ?></td>
        <td><?= htmlspecialchars($s['class_name']) ?></td>
        <td><?= htmlspecialchars($s['batch_name']) ?></td>
        <td><?= $s['percentage'] ?>%</td>
      </tr>
      <?php endwhile; ?>
    </table>

  </div>

</div>

</body>
</html>