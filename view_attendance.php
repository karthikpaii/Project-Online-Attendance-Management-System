<?php
session_start();
if(!isset($_SESSION['college_code'])) die("Session expired");

$college_code = $_SESSION['college_code'];
$conn = new mysqli("localhost","root","","users");
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);


// ===== HANDLE JSON REQUEST (SAVE) =====
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if(isset($data['action']) && $data['action'] == 'save_attendance'){
    foreach($data['updates'] as $u){
        $rollno = $u['rollno'];
        $status = $u['status'];

        $check = $conn->prepare("SELECT id FROM attendance WHERE date=? AND batch_name=? AND class_name=? AND subject=? AND student_roll=? AND college_code=?");
        $check->bind_param("ssssss",$data['date'],$data['batch'],$data['cls'],$data['subject'],$rollno,$college_code);
        $check->execute();
        $res = $check->get_result();

        if($res->num_rows > 0){
            $upd = $conn->prepare("UPDATE attendance SET status=? WHERE date=? AND batch_name=? AND class_name=? AND subject=? AND student_roll=? AND college_code=?");
            $upd->bind_param("issssss",$status,$data['date'],$data['batch'],$data['cls'],$data['subject'],$rollno,$college_code);
            $upd->execute();
        } else {
            $ins = $conn->prepare("INSERT INTO attendance(date,batch_name,class_name,subject,student_roll,status,college_code) VALUES(?,?,?,?,?,?,?)");
            $ins->bind_param("sssssii",$data['date'],$data['batch'],$data['cls'],$data['subject'],$rollno,$status,$college_code);
            $ins->execute();
        }
    }
    echo json_encode(["msg"=>"Saved"]);
    exit();
}

// ===== GET CLASSES =====
if(isset($_GET['get_classes'])){
    $batch = $_GET['batch'];
    $stmt = $conn->prepare("SELECT classes FROM batches WHERE batch_name=? AND college_code=?");
    $stmt->bind_param("ss",$batch,$college_code);
    $stmt->execute();
    $res = $stmt->get_result();

    $classes = [];
    while($row = $res->fetch_assoc()){
        $split = explode(',', $row['classes']);
        foreach($split as $c){
            $c = trim($c);
            if($c) $classes[] = $c;
        }
    }
    echo json_encode($classes);
    exit();
}

// ===== GET STUDENTS =====
if(isset($_GET['get_students'])){
    $batch = $_GET['batch'];
    $cls = $_GET['cls'];

    $stmt = $conn->prepare("SELECT rollno,name FROM students WHERE batch_name=? AND class_name=? AND college_code=?");
    $stmt->bind_param("sss",$batch,$cls,$college_code);
    $stmt->execute();
    $res = $stmt->get_result();

    $students = [];
    while($row = $res->fetch_assoc()){
        $students[] = $row;
    }

    echo json_encode($students);
    exit();
}

// ===== GET ATTENDANCE =====
if(isset($_GET['get_attendance'])){
    $stmt = $conn->prepare("SELECT rollno,status FROM attendance WHERE date=? AND batch=? AND class=? AND subject=? AND college_code=?");
    $stmt->bind_param("sssss",$_GET['date'],$_GET['batch'],$_GET['cls'],$_GET['subject'],$college_code);
    $stmt->execute();
    $res = $stmt->get_result();

    $att = [];
    while($row = $res->fetch_assoc()){
        $att[$row['rollno']] = $row['status'];
    }

    echo json_encode($att);
    exit();
}

// ===== NORMAL PAGE LOAD BELOW =====
header("Content-Type: text/html");

// batches
$batches = $conn->query("SELECT DISTINCT batch_name FROM batches WHERE college_code='$college_code'");

// subjects
$subjects = $conn->query("SELECT DISTINCT subject_name FROM subjects WHERE college_code='$college_code'");
?>

<!DOCTYPE html>
<html>
<head>
<title>Attendance</title>
<style>
body{font-family:Arial;background:#f4f4f4;}
.container{width:80%;margin:20px auto;background:#fff;padding:20px;border-radius:8px;}
select,input{width:100%;padding:8px;margin:8px 0;}
table{width:100%;border-collapse:collapse;margin-top:10px;}
th,td{border:1px solid #ccc;padding:8px;}
.btn{padding:10px;background:blue;color:#fff;border:none;cursor:pointer;}
</style>
</head>

<body>
<div class="container">
<h2>Attendance</h2>

<input type="date" id="date">

<select id="batch">
<option value="">Batch</option>
<?php while($b = $batches->fetch_assoc()): ?>
<option><?= $b['batch_name'] ?></option>
<?php endwhile; ?>
</select>

<select id="class"><option>Class</option></select>

<select id="subject">
<option value="">Subject</option>
<?php while($s = $subjects->fetch_assoc()): ?>
<option><?= $s['subject_name'] ?></option>
<?php endwhile; ?>
</select>

<button class="btn" onclick="loadData()">Load</button>

<div id="table"></div>
</div>

<script>
document.getElementById('batch').onchange = function(){
    let b = this.value;
    fetch(`view_attendance.php?get_classes=1&batch=${b}`)
    .then(r=>r.json())
    .then(data=>{
        let c = document.getElementById('class');
        c.innerHTML='';
        data.forEach(x=>{
            let o=document.createElement('option');
            o.textContent=x;
            c.appendChild(o);
        });
    });
}

function loadData(){
    let date = document.getElementById('date').value;
    let batch = document.getElementById('batch').value;
    let cls = document.getElementById('class').value;
    let subject = document.getElementById('subject').value;

    fetch(`view_attendance.php?get_students=1&batch=${batch}&cls=${cls}`)
    .then(r=>r.json())
    .then(students=>{

        fetch(`view_attendance.php?get_attendance=1&date=${date}&batch=${batch}&cls=${cls}&subject=${subject}`)
        .then(r=>r.json())
        .then(att=>{

            let html = "<table><tr><th>Roll</th><th>Name</th><th>Present</th></tr>";

            students.forEach(s=>{
                let checked = att[s.rollno]==1 ? "checked":"";
                html += `<tr>
                    <td>${s.rollno}</td>
                    <td>${s.name}</td>
                    <td><input type="checkbox" data-roll="${s.rollno}" ${checked}></td>
                </tr>`;
            });

            html += "</table><button onclick='save()'>Save</button>";
            document.getElementById('table').innerHTML = html;
        });
    });
}

function save(){
    let updates=[];
    document.querySelectorAll("input[type=checkbox]").forEach(cb=>{
        updates.push({
            rollno: cb.dataset.roll,
            status: cb.checked ? 1 : 0
        });
    });

    fetch('view_attendance.php',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({
            action:'save_attendance',
            date: document.getElementById('date').value,
            batch: document.getElementById('batch').value,
            cls: document.getElementById('class').value,
            subject: document.getElementById('subject').value,
            updates
        })
    }).then(r=>r.json()).then(d=>alert(d.msg));
}
</script>

</body>
</html>