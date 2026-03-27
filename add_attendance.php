<?php
session_start();
if(!isset($_SESSION['college_code'])) die("Session expired");

$college_code = $_SESSION['college_code'];
$conn = new mysqli("localhost","root","","users");
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

// ===== AJAX: Return classes for a batch =====
if(isset($_GET['get_classes']) && isset($_GET['batch'])) {
    $batch = $_GET['batch'];
    $stmt = $conn->prepare("SELECT classes FROM batches WHERE batch_name=? AND college_code=?");
    $stmt->bind_param("ss", $batch, $college_code);
    $stmt->execute();
    $result = $stmt->get_result();

    $classes = [];
    while($row = $result->fetch_assoc()){
        if(!empty($row['classes'])){
            $clsArray = explode(',', $row['classes']); // comma-separated classes
            foreach($clsArray as $cls){
                $cls = trim($cls);
                if($cls) $classes[] = $cls;
            }
        }
    }

    echo json_encode($classes);
    exit();
}

// ===== AJAX: Return students for a batch and class =====
if(isset($_GET['get_students']) && isset($_GET['batch']) && isset($_GET['class'])){
    $batch = $_GET['batch'];
    $class = $_GET['class'];

    $stmt = $conn->prepare("SELECT rollno, name FROM students WHERE batch_name=? AND class_name=? AND college_code=?");
    $stmt->bind_param("sss", $batch, $class, $college_code);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];
    while($row = $result->fetch_assoc()){
        $students[] = ['rollno'=>$row['rollno'], 'name'=>$row['name']];
    }

    echo json_encode($students);
    exit();
}

// Fetch batches for dropdown
$batch_result = $conn->prepare("SELECT DISTINCT batch_name FROM batches WHERE college_code=?");
$batch_result->bind_param("s",$college_code);
$batch_result->execute();
$batch_result = $batch_result->get_result();

// Fetch subjects for dropdown
$subject_result = $conn->prepare("SELECT DISTINCT subject_name FROM subjects WHERE college_code=?");
$subject_result->bind_param("s",$college_code);
$subject_result->execute();
$subject_result = $subject_result->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mark Attendance</title>
    <style>
        body { font-family: Arial,sans-serif; background:#f4f4f4; }
        h2 { text-align:center; font-size:36px; margin-top:20px; }
        .container { width:60%; margin:30px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.1);}
        label { font-weight:bold; margin-top:10px; display:block; }
        select, input[type="date"], input[type="text"] { padding:8px; width:100%; margin-bottom:15px; border-radius:5px; border:1px solid #ccc;}
        .btn { padding:10px 20px; background:#007bff; color:#fff; border:none; border-radius:5px; cursor:pointer;}
        .btn:hover { background:#0056b3; }
        #studentsList { margin-top:20px; }
        .studentCheckbox { margin-right:10px; }
        #message { display:none; position:fixed; top:20px; right:20px; padding:15px; border-radius:8px; color:#fff; z-index:999; }
        #message.success { background:#28a745; }
        #message.error { background:#dc3545; }
    </style>
</head>
<body>

<h2>Mark Attendance</h2>
<div id="message"></div>

<div class="container">
<form method="post" action="save_attendance.php">

    <label>Date:</label>
    <input type="date" name="date" required>

    <label>Select Batch:</label>
    <select name="batch" id="batch" required>
        <option value="">--Select Batch--</option>
        <?php while($row = $batch_result->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($row['batch_name']) ?>"><?= htmlspecialchars($row['batch_name']) ?></option>
        <?php endwhile; ?>
    </select>

    <label>Select Class:</label>
    <select name="class" id="class" required>
        <option value="">--Select Class--</option>
    </select>

    <label>Select Subject:</label>
    <select name="subject" id="subjectSelect" required>
        <option value="">--Select Subject--</option>
        <?php while($row = $subject_result->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($row['subject_name']) ?>"><?= htmlspecialchars($row['subject_name']) ?></option>
        <?php endwhile; ?>
    </select>

    <label>Or Add New Subject:</label>
    <input type="text" id="newSubject" placeholder="Enter new subject">
    <button type="button" class="btn" onclick="addSubject()">Add Subject</button>

    <div id="studentsList">
        <!-- Student checkboxes will load here -->
    </div>

    <input type="submit" class="btn" value="Save Attendance">
</form>
</div>

<script>
// Add new subject to dropdown
function addSubject() {
    const newSubjectInput = document.getElementById('newSubject');
    const subjectSelect = document.getElementById('subjectSelect');
    const subjectName = newSubjectInput.value.trim();
    if(!subjectName) return alert('Enter subject name');

    for(let opt of subjectSelect.options){
        if(opt.value.toLowerCase() === subjectName.toLowerCase()){
            alert('Subject already exists');
            return;
        }
    }

    let newOption = document.createElement('option');
    newOption.value = subjectName;
    newOption.textContent = subjectName;
    newOption.selected = true;
    subjectSelect.appendChild(newOption);
    newSubjectInput.value = '';
    alert('Subject added! It will be saved with attendance.');
}

// Load classes when batch changes
document.getElementById('batch').addEventListener('change', function(){
    const batch = this.value;
    const classSelect = document.getElementById('class');
    classSelect.innerHTML = '<option value="">--Select Class--</option>';
    document.getElementById('studentsList').innerHTML = '';

    if(!batch) return;

    fetch('add_attendance.php?get_classes=1&batch=' + encodeURIComponent(batch))
    .then(res => res.json())
    .then(data => {
        data.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c;
            opt.textContent = c;
            classSelect.appendChild(opt);
        });
    });
});

// Load students when class changes
document.getElementById('class').addEventListener('change', function(){
    const batch = document.getElementById('batch').value;
    const cls = this.value;
    const studentsDiv = document.getElementById('studentsList');
    studentsDiv.innerHTML = '';

    if(!batch || !cls) return;

    fetch('add_attendance.php?get_students=1&batch=' + encodeURIComponent(batch) + '&class=' + encodeURIComponent(cls))
    .then(res => res.json())
    .then(data => {
        if(data.length === 0){
            studentsDiv.innerHTML = 'No students in this class';
            return;
        }

        // Select all
        let selectAll = document.createElement('input');
        selectAll.type = 'checkbox';
        selectAll.id = 'selectAll';
        let labelAll = document.createElement('label');
        labelAll.textContent = ' Select All';
        labelAll.htmlFor = 'selectAll';
        studentsDiv.appendChild(selectAll);
        studentsDiv.appendChild(labelAll);
        studentsDiv.appendChild(document.createElement('br'));

        selectAll.addEventListener('change', function(){
            document.querySelectorAll('.studentCheckbox').forEach(cb => cb.checked = this.checked);
        });

        // Individual students
        data.forEach(student => {
            let cb = document.createElement('input');
            cb.type = 'checkbox';
            cb.name = 'students[]';
            cb.value = student.rollno;
            cb.className = 'studentCheckbox';

            let label = document.createElement('label');
            label.textContent = student.name + ' (' + student.rollno + ')';
            label.prepend(cb);

            studentsDiv.appendChild(label);
            studentsDiv.appendChild(document.createElement('br'));
        });
    });
});
</script>

</body>
</html>