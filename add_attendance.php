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
            $clsArray = explode(',', $row['classes']);
            foreach($clsArray as $cls){
                $cls = trim($cls);
                if($cls) $classes[] = $cls;
            }
        }
    }
    echo json_encode($classes);
    exit();
}

// ===== AJAX: Return students for batch + class =====
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

// Fetch batches
$batch_result = $conn->prepare("SELECT DISTINCT batch_name FROM batches WHERE college_code=?");
$batch_result->bind_param("s",$college_code);
$batch_result->execute();
$batch_result = $batch_result->get_result();

// Fetch subjects
$subject_result = $conn->prepare("SELECT DISTINCT subject_name FROM subjects WHERE college_code=?");
$subject_result->bind_param("s",$college_code);
$subject_result->execute();
$subject_result = $subject_result->get_result();
$subjects = [];
while($row = $subject_result->fetch_assoc()){
    $subjects[] = $row['subject_name'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mark Attendance</title>
    <style>
        body{ font-family: Arial,sans-serif; background:#f4f4f4;}
        h2{ text-align:center; margin-top:20px;}
        .container{ width:80%; margin:20px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.1);}
        label{ font-weight:bold; display:block; margin-top:10px;}
        select, input[type="date"], input[type="text"]{ padding:8px; width:100%; margin-bottom:10px; border-radius:5px; border:1px solid #ccc;}
        .btn{ padding:10px 20px; background:#007bff; color:#fff; border:none; border-radius:5px; cursor:pointer; margin-top:10px;}
        .btn:hover{ background:#0056b3;}
        table{ width:100%; border-collapse: collapse; margin-top:15px;}
        th, td{ border:1px solid #ccc; padding:8px; text-align:left;}
        th{ background:#eee;}
        #message{ display:none; position:fixed; top:20px; right:20px; padding:15px; border-radius:8px; color:#fff; z-index:999;}
        #message.success{ background:#28a745;}
        #message.error{ background:#dc3545;}
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
        <?php foreach($subjects as $sub): ?>
            <option value="<?= htmlspecialchars($sub) ?>"><?= htmlspecialchars($sub) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Or Add New Subject:</label>
    <input type="text" id="newSubject" placeholder="Enter new subject">
    <button type="button" class="btn" onclick="addSubject()">Add Subject</button>

    <div id="studentsTable">
       
    </div>

    <input type="submit" class="btn" value="Save Attendance">
</form>
</div>

<script>
// Add new subject
function addSubject(){
    const input = document.getElementById('newSubject');
    const select = document.getElementById('subjectSelect');
    const name = input.value.trim();
    if(!name) return alert('Enter subject name');
    for(let opt of select.options){ if(opt.value.toLowerCase()===name.toLowerCase()){ alert('Subject exists'); return; } }
    let newOpt = document.createElement('option');
    newOpt.value = name; newOpt.textContent = name; newOpt.selected = true;
    select.appendChild(newOpt);
    input.value='';
    alert('Subject added!');
}

// Load classes when batch changes
document.getElementById('batch').addEventListener('change', function(){
    const batch = this.value;
    const classSelect = document.getElementById('class');
    classSelect.innerHTML = '<option value="">--Select Class--</option>';
    document.getElementById('studentsTable').innerHTML='';
    if(!batch) return;
    fetch('add_attendance.php?get_classes=1&batch=' + encodeURIComponent(batch))
    .then(res=>res.json())
    .then(data=>{
        data.forEach(c=>{
            let opt = document.createElement('option'); opt.value=c; opt.textContent=c;
            classSelect.appendChild(opt);
        });
    });
});

// Load students when class changes
document.getElementById('class').addEventListener('change', function(){
    const batch = document.getElementById('batch').value;
    const cls = this.value;
    const tableDiv = document.getElementById('studentsTable');
    tableDiv.innerHTML='';
    if(!batch || !cls) return;

    fetch('add_attendance.php?get_students=1&batch='+encodeURIComponent(batch)+'&class='+encodeURIComponent(cls))
    .then(res=>res.json())
    .then(data=>{
        if(data.length===0){ tableDiv.innerHTML='No students in this class'; return; }

        let table = document.createElement('table');
        let thead = document.createElement('thead');
        thead.innerHTML='<tr><th><input type="checkbox" id="selectAll"> All</th><th>Roll No</th><th>Name</th><th>Batch</th><th>Class</th></tr>';
        table.appendChild(thead);
        let tbody = document.createElement('tbody');

        data.forEach(student=>{
            let tr = document.createElement('tr');
            tr.innerHTML=`<td><input type="checkbox" name="students[]" class="studentCheckbox" value="${student.rollno}"></td>
                           <td>${student.rollno}</td>
                           <td>${student.name}</td>
                           <td>${batch}</td>
                           <td>${cls}</td>`;
            tbody.appendChild(tr);
        });
        table.appendChild(tbody);
        tableDiv.appendChild(table);

        // Select all functionality
        document.getElementById('selectAll').addEventListener('change', function(){
            document.querySelectorAll('.studentCheckbox').forEach(cb=>cb.checked=this.checked);
        });
    });
});


document.querySelector("form").addEventListener("submit", function(e){
    e.preventDefault(); // stop reload

    const form = this;
    const formData = new FormData(form);

    fetch("save_attendance.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {

        if(data.trim() === "success"){
            showMessage("Attendance marked successfully!", "success");

            form.reset();
            document.getElementById("studentsTable").innerHTML = "";

        } else {
            showMessage("Failed to save attendance!", "error");
        }

    })
    .catch(() => {
        showMessage("Server error!", "error");
    });
});



function showMessage(text, type){
    const msg = document.getElementById("message");

    msg.innerText = text;
    msg.className = type; 
    msg.style.display = "block";

    setTimeout(()=>{
        msg.style.display = "none";
    }, 3000);
}
</script>
</body>
</html>