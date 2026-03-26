<?php
session_start();

if (!isset($_SESSION['user']) || !isset($_SESSION['college_code'])) {
    header("Location: sign.html");
    exit();
}

$college_code = $_SESSION['college_code'];

/* ===== AJAX DELETE ===== */
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = intval($_POST['id']);
    $conn = new mysqli("localhost","root","","users");
    if ($conn->connect_error) die("Connection failed: ".$conn->connect_error);

    $stmt = $conn->prepare("DELETE FROM students WHERE id=? AND college_code=?");
    $stmt->bind_param("is", $id, $college_code);
    echo $stmt->execute() ? "success" : "error";
    $stmt->close();
    $conn->close();
    exit();
}

/* ===== AJAX UPDATE ===== */
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $rollno = $_POST['rollno'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    $conn = new mysqli("localhost","root","","users");
    if ($conn->connect_error) die("Connection failed: ".$conn->connect_error);

    $stmt = $conn->prepare("UPDATE students SET name=?, rollno=?, phone=?, email=? WHERE id=? AND college_code=?");
    $stmt->bind_param("sssiis", $name, $rollno, $phone, $email, $id, $college_code);
    echo $stmt->execute() ? "success" : "error";
    $stmt->close();
    $conn->close();
    exit();
}

/* ===== FETCH STUDENTS ===== */
$conn = new mysqli("localhost","root","","users");
if ($conn->connect_error) die("Connection failed: ".$conn->connect_error);

$batch = isset($_GET['batch']) ? $_GET['batch'] : '';
$class = isset($_GET['class']) ? $_GET['class'] : '';

$sql = "SELECT * FROM students WHERE college_code=?";
$params = [$college_code];
$types = "s";

if (!empty($batch)) {
    $sql .= " AND batch_name=?";
    $types .= "s";
    $params[] = $batch;
}

if (!empty($class)) {
    $sql .= " AND class_name=?";
    $types .= "s";
    $params[] = $class;
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Student List</title>
    <style>
        body { font-family: Arial; background: #e8f0f2; margin:0; padding:0; }
        h2 { text-align:center; margin-top:30px; }
        .container { width:90%; margin:20px auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1);}
        table { width:100%; border-collapse: collapse; margin-top:20px;}
        th, td { border:1px solid #ddd; padding:10px; text-align:center;}
        th { background:#007bff; color:#fff; }
        tr:hover { background:#eee; }
        input[type=text] { width:90%; padding:5px;}
        .action-button { display:flex; gap:10px; justify-content:center; }
        .btn { padding:6px 10px; border:none; border-radius:5px; cursor:pointer; color:#fff; background:#007bff;}
        .btn:hover { background:#0056b3; }
        .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;}
        .modal-content { background:#fff; width:300px; margin:15% auto; padding:20px; border-radius:10px; text-align:center;}
        .modal-buttons { display:flex; justify-content:space-around; margin-top:15px;}
        .yes-btn { background:#dc3545; color:#fff; padding:8px 15px; border:none; border-radius:5px; cursor:pointer;}
        .no-btn { background:#6c757d; color:#fff; padding:8px 15px; border:none; border-radius:5px; cursor:pointer;}
    </style>
</head>
<body>
<div class="container">
<h2>Student List</h2>

<table id="student-table">
    <tr>
        <th>Roll No</th>
        <th>Name</th>
        <th>Batch</th>
        <th>Class</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Action</th>
    </tr>

<?php
while($row = $result->fetch_assoc()) {
    echo "<tr id='row-{$row['id']}'>
            <td>
                <span id='roll-text-{$row['id']}'>{$row['rollno']}</span>
                <input type='text' id='roll-input-{$row['id']}' value='{$row['rollno']}' style='display:none;'>
            </td>
            <td>
                <span id='name-text-{$row['id']}'>{$row['name']}</span>
                <input type='text' id='name-input-{$row['id']}' value='{$row['name']}' style='display:none;'>
            </td>
            <td>{$row['batch_name']}</td>
            <td>{$row['class_name']}</td>
            <td>
                <span id='phone-text-{$row['id']}'>{$row['phone']}</span>
                <input type='text' id='phone-input-{$row['id']}' value='{$row['phone']}' style='display:none;'>
            </td>
            <td>
                <span id='email-text-{$row['id']}'>{$row['email']}</span>
                <input type='text' id='email-input-{$row['id']}' value='{$row['email']}' style='display:none;'>
            </td>
            <td>
                <div class='action-button'>
                    <button class='btn delete-btn' onclick='deleteStudent({$row['id']})'>Delete</button>
                    <button class='btn update-btn' id='edit-btn-{$row['id']}' onclick='editRow({$row['id']})'>Update</button>
                    <button class='btn update-btn' id='save-btn-{$row['id']}' onclick='saveRow({$row['id']})' style='display:none;'>Save</button>
                </div>
            </td>
          </tr>";
}
$conn->close();
?>
</table>
</div>

<!-- DELETE MODAL -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <p>Are you sure you want to delete?</p>
        <div class="modal-buttons">
            <button class="yes-btn" onclick="confirmDelete()">Yes</button>
            <button class="no-btn" onclick="closeModal()">Cancel</button>
        </div>
    </div>
</div>

<script>
let deleteId = null;

function deleteStudent(id) {
    deleteId = id;
    document.getElementById("deleteModal").style.display = "block";
}

function closeModal() {
    document.getElementById("deleteModal").style.display = "none";
}

function confirmDelete() {
    fetch("", {
        method:"POST",
        headers: {"Content-Type":"application/x-www-form-urlencoded"},
        body:"action=delete&id=" + deleteId
    })
    .then(res=>res.text())
    .then(data=>{
        if(data.trim() === "success") {
            document.getElementById("row-"+deleteId).remove();
        } else {
            alert("Delete failed!");
        }
        closeModal();
    });
}

function editRow(id) {
    document.getElementById("roll-text-"+id).style.display="none";
    document.getElementById("name-text-"+id).style.display="none";
    document.getElementById("phone-text-"+id).style.display="none";
    document.getElementById("email-text-"+id).style.display="none";

    document.getElementById("roll-input-"+id).style.display="inline";
    document.getElementById("name-input-"+id).style.display="inline";
    document.getElementById("phone-input-"+id).style.display="inline";
    document.getElementById("email-input-"+id).style.display="inline";

    document.getElementById("edit-btn-"+id).style.display="none";
    document.getElementById("save-btn-"+id).style.display="inline";
}

function saveRow(id) {
    let name = encodeURIComponent(document.getElementById("name-input-"+id).value);
    let rollno = encodeURIComponent(document.getElementById("roll-input-"+id).value);
    let phone = encodeURIComponent(document.getElementById("phone-input-"+id).value);
    let email = encodeURIComponent(document.getElementById("email-input-"+id).value);

    fetch("",{
        method:"POST",
        headers: {"Content-Type":"application/x-www-form-urlencoded"},
        body:"action=update&id="+id+"&name="+name+"&rollno="+rollno+"&phone="+phone+"&email="+email
    })
    .then(res=>res.text())
    .then(data=>{
        if(data.trim() === "success") {
            document.getElementById("roll-text-"+id).innerText = decodeURIComponent(rollno);
            document.getElementById("name-text-"+id).innerText = decodeURIComponent(name);
            document.getElementById("phone-text-"+id).innerText = decodeURIComponent(phone);
            document.getElementById("email-text-"+id).innerText = decodeURIComponent(email);

            document.getElementById("roll-text-"+id).style.display="inline";
            document.getElementById("name-text-"+id).style.display="inline";
            document.getElementById("phone-text-"+id).style.display="inline";
            document.getElementById("email-text-"+id).style.display="inline";

            document.getElementById("roll-input-"+id).style.display="none";
            document.getElementById("name-input-"+id).style.display="none";
            document.getElementById("phone-input-"+id).style.display="none";
            document.getElementById("email-input-"+id).style.display="none";

            document.getElementById("edit-btn-"+id).style.display="inline";
            document.getElementById("save-btn-"+id).style.display="none";
        } else {
            alert("Update failed!");
        }
    });
}
</script>

</body>
</html>