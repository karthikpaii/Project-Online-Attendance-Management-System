<?php
session_start();
if(!isset($_SESSION['college_code'])) {
    echo "error";
    exit();
}

$college_code = $_SESSION['college_code'];
$conn = new mysqli("localhost","root","","users");

if($conn->connect_error){
    echo "error";
    exit();
}

$date = $_POST['date'] ?? '';
$batch = $_POST['batch'] ?? '';
$subject = $_POST['subject'] ?? '';
$students = $_POST['students'] ?? [];

if(!$date || !$batch || !$subject){
    echo "error";
    exit();
}


$stmt = $conn->prepare("SELECT id FROM subjects WHERE subject_name=? AND college_code=?");
$stmt->bind_param("ss",$subject,$college_code);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    $insertSubj = $conn->prepare("INSERT INTO subjects (subject_name, college_code) VALUES (?, ?)");
    $insertSubj->bind_param("ss",$subject,$college_code);
    $insertSubj->execute();
}


$stmt = $conn->prepare("SELECT rollno, name, class_name FROM students WHERE batch_name=? AND college_code=?");
$stmt->bind_param("ss",$batch,$college_code);
$stmt->execute();
$result = $stmt->get_result();


while($row = $result->fetch_assoc()){
    $status = in_array($row['rollno'],$students) ? 'Present' : 'Absent';

    $insert = $conn->prepare("INSERT INTO attendance 
    (college_code,batch_name,class_name,subject,student_roll,student_name,date,status) 
    VALUES (?,?,?,?,?,?,?,?)");

    $insert->bind_param("ssssssss",
        $college_code,
        $batch,
        $row['class_name'],
        $subject,
        $row['rollno'],
        $row['name'],
        $date,
        $status
    );

    $insert->execute();
}

echo "success";
exit();
?>