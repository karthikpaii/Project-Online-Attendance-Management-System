<?php
session_start();
if(!isset($_SESSION['college_code'])) die("Session expired");

$college_code = $_SESSION['college_code'];
$conn = new mysqli("localhost","root","","users");

if(!isset($_GET['batch'])) exit;

$batch = $_GET['batch'];
$stmt = $conn->prepare("SELECT rollno, name FROM students WHERE batch_name=? AND college_code=?");
$stmt->bind_param("ss",$batch,$college_code);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while($row = $result->fetch_assoc()){
    $students[] = $row;
}
echo json_encode($students);
?>