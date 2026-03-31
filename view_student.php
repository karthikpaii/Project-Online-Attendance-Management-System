<?php
session_start();

// 1. Basic Security Check
if (!isset($_SESSION['user']) || $_SESSION['role'] !== "admin") {
    header("Location: sign.html");
    exit();
}

$college_code = $_SESSION['college_code'];
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "users";

/* AJAX: FETCH CLASSES FOR SELECTED BATCH */
if (isset($_GET['get_classes_for_batch'])) {
    $batch = $_GET['get_classes_for_batch'];
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    $stmt = $conn->prepare("SELECT DISTINCT classes FROM batches WHERE batch_name=? AND college_code=? ORDER BY classes ASC");
    $stmt->bind_param("ss", $batch, $college_code);
    $stmt->execute();
    $res = $stmt->get_result();
    
    echo '<option value="">-- Select Class --</option>';
    while($row = $res->fetch_assoc()) {
        echo '<option value="'.htmlspecialchars($row['classes']).'">'.htmlspecialchars($row['classes']).'</option>';
    }
    $stmt->close();
    $conn->close();
    exit();
}

/* ===== AJAX: DELETE STUDENT ===== */
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = intval($_POST['id']);
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $stmt = $conn->prepare("DELETE FROM students WHERE id=? AND college_code=?");
    $stmt->bind_param("is", $id, $college_code);
    echo $stmt->execute() ? "success" : "error";
    $stmt->close();
    $conn->close();
    exit();
}

/* ===== UPDATE STUDENT ===== */
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $rollno = $_POST['rollno'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $stmt = $conn->prepare("UPDATE students SET name=?, rollno=?, phone=?, email=? WHERE id=? AND college_code=?");
    $stmt->bind_param("ssssis", $name, $rollno, $phone, $email, $id, $college_code);
    echo $stmt->execute() ? "success" : "error";
    $stmt->close();
    $conn->close();
    exit();
}

/* ===== FETCH STUDENT TABLE ===== */
if (isset($_GET['batch']) && isset($_GET['class'])) {
    $batch_name = $_GET['batch'];
    $class_name = $_GET['class'];

    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $stmt = $conn->prepare("SELECT * FROM students WHERE batch_name=? AND class_name=? AND college_code=? ORDER BY id ASC");
    $stmt->bind_param("sss", $batch_name, $class_name, $college_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        // Added a Search Bar for the table
        echo '<div style="margin-bottom:10px;"><input type="text" id="tableSearch" onkeyup="filterTable()" placeholder="Search names or roll numbers..." style="width:100%; padding:10px; border-radius:5px; border:1px solid #ddd;"></div>';
        
        echo '<table id="studentTable">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Roll No</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>';
        while($row = $result->fetch_assoc()){
            $sid = $row['id'];
            echo '<tr id="row-'.$sid.'">
                    <td>'.$sid.'</td>
                    <td><span id="name-text-'.$sid.'">'.htmlspecialchars($row['name']).'</span>
                        <input type="text" id="name-input-'.$sid.'" value="'.htmlspecialchars($row['name']).'" style="display:none;"></td>
                    <td><span id="roll-text-'.$sid.'">'.htmlspecialchars($row['rollno']).'</span>
                        <input type="text" id="roll-input-'.$sid.'" value="'.htmlspecialchars($row['rollno']).'" style="display:none;"></td>
                    <td><span id="phone-text-'.$sid.'">'.htmlspecialchars($row['phone']).'</span>
                        <input type="text" id="phone-input-'.$sid.'" value="'.htmlspecialchars($row['phone']).'" style="display:none;"></td>
                    <td><span id="email-text-'.$sid.'">'.htmlspecialchars($row['email']).'</span>
                        <input type="text" id="email-input-'.$sid.'" value="'.htmlspecialchars($row['email']).'" style="display:none;"></td>
                    <td class="action-button">
                        <button class="btn" id="edit-btn-'.$sid.'" onclick="editRow('.$sid.')">Edit</button>
                        <button class="btn" id="save-btn-'.$sid.'" onclick="saveRow('.$sid.')" style="display:none; background:#28a745;">Save</button>
                        <button class="btn" style="background:#dc3545;" onclick="deleteStudent('.$sid.')">Delete</button>
                    </td>
                  </tr>';
        }
        echo '</table>';
    } else {
        echo "<p style='text-align:center; margin-top:20px;'>No students found for this batch and class.</p>";
    }
    $stmt->close();
    $conn->close();
    exit();
}

/* =====  FETCH BATCHES ===== */
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
$batch_result = $conn->query("SELECT DISTINCT batch_name FROM batches WHERE college_code='$college_code' ORDER BY batch_name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Students</title>
    <style>
        body { font-family: Arial, sans-serif; background: #e8f0f2; margin:0; padding:20px;}
        h1 { text-align:center; color: #333;}
        .filter-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); display: flex; justify-content: center; align-items: center; gap: 15px; margin-bottom: 20px;}
        select, input[type=submit] { padding:10px; border-radius:5px; border:1px solid #ccc; font-size:14px;}
        input[type=submit] { cursor:pointer; background:#007bff; color:#fff; border:none; transition: 0.3s; }
        input[type=submit]:hover { background:#0056b3; }
        table { width:100%; border-collapse:collapse; margin-top:10px; background:#fff;}
        th, td { border:1px solid #ddd; padding:12px; text-align:center;}
        th { background:#007bff; color:#fff;}
        .action-button { display:flex; justify-content:center; gap:8px;}
        .btn { padding:6px 12px; border:none; border-radius:4px; cursor:pointer; color:#fff; background:#007bff;}
        .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;}
        .modal-content { background:#fff; width:300px; margin:15% auto; padding:20px; border-radius:10px; text-align:center;}
        .modal-buttons { display:flex; justify-content:space-around; margin-top:15px;}
        .yes-btn { background:#dc3545; color:#fff; padding:8px 15px; border:none; border-radius:5px; cursor:pointer;}
        .no-btn { background:#6c757d; color:#fff; padding:8px 15px; border:none; border-radius:5px; cursor:pointer;}
    </style>
</head>
<body>

<h1>View Student List</h1>

<div class="filter-container">
    <form id="batchForm" style="display: contents;">
        <label>Batch:</label>
        <select name="batch" id="batchSelect" required>
            <option value="">-- Select Batch --</option>
            <?php while($b = $batch_result->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($b['batch_name']) ?>"><?= htmlspecialchars($b['batch_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label>Class:</label>
        <select name="class" id="classSelect" required>
            <option value="">-- Select Batch First --</option>
        </select>

        <input type="submit" value="Load Student Data">
    </form>
</div>

<div id="studentContainer"></div>

<div id="deleteModal" class="modal">
    <div class="modal-content">
        <p>Are you sure you want to delete this record?</p>
        <div class="modal-buttons">
            <button class="yes-btn" onclick="confirmDelete()">Yes, Delete</button>
            <button class="no-btn" onclick="closeModal()">Cancel</button>
        </div>
    </div>
</div>

<script>
let deleteId = null;

// DYNAMIC CLASS LOADING
document.getElementById("batchSelect").addEventListener("change", function() {
    const batchName = this.value;
    const classSelect = document.getElementById("classSelect");
    
    if (batchName === "") {
        classSelect.innerHTML = '<option value="">-- Select Batch First --</option>';
        return;
    }

    fetch("?get_classes_for_batch=" + encodeURIComponent(batchName))
        .then(res => res.text())
        .then(html => {
            classSelect.innerHTML = html;
        });
});

// VIEW STUDENTS
document.getElementById("batchForm").addEventListener("submit", function(e){
    e.preventDefault();
    const batch = document.getElementById("batchSelect").value;
    const cls = document.getElementById("classSelect").value;
    
    fetch("?batch=" + encodeURIComponent(batch) + "&class=" + encodeURIComponent(cls))
    .then(res=>res.text())
    .then(html=>{
        document.getElementById("studentContainer").innerHTML = html;
    });
});

// SEARCH FILTER LOGIC
function filterTable() {
    const input = document.getElementById("tableSearch");
    const filter = input.value.toUpperCase();
    const table = document.getElementById("studentTable");
    const tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let textContent = tr[i].textContent || tr[i].innerText;
        tr[i].style.display = textContent.toUpperCase().indexOf(filter) > -1 ? "" : "none";
    }
}

// DELETE LOGIC
function deleteStudent(id){
    deleteId = id;
    document.getElementById("deleteModal").style.display = "block";
}
function closeModal(){ document.getElementById("deleteModal").style.display = "none"; }
function confirmDelete(){
    fetch("",{
        method:"POST",
        headers: {"Content-Type":"application/x-www-form-urlencoded"},
        body:"action=delete&id="+deleteId
    }).then(res=>res.text())
      .then(data=>{
          if(data.trim()==="success"){
              document.getElementById("row-"+deleteId).remove();
          } else alert("Delete failed!");
          closeModal();
      });
}

// EDIT/SAVE LOGIC
function editRow(id){
    ["name","roll","phone","email"].forEach(field=>{
        document.getElementById(`${field}-text-${id}`).style.display="none";
        document.getElementById(`${field}-input-${id}`).style.display="inline-block";
        document.getElementById(`${field}-input-${id}`).style.width="90%";
    });
    document.getElementById("edit-btn-"+id).style.display="none";
    document.getElementById("save-btn-"+id).style.display="inline-block";
}

function saveRow(id){
    const name = document.getElementById(`name-input-${id}`).value;
    const roll = document.getElementById(`roll-input-${id}`).value;
    const phone = document.getElementById(`phone-input-${id}`).value;
    const email = document.getElementById(`email-input-${id}`).value;

    const params = new URLSearchParams();
    params.append('action', 'update');
    params.append('id', id);
    params.append('name', name);
    params.append('rollno', roll);
    params.append('phone', phone);
    params.append('email', email);

    fetch("",{
        method:"POST",
        headers: {"Content-Type":"application/x-www-form-urlencoded"},
        body: params.toString()
    }).then(res=>res.text())
      .then(resp=>{
          if(resp.trim()==="success"){
              document.getElementById(`name-text-${id}`).innerText = name;
              document.getElementById(`roll-text-${id}`).innerText = roll;
              document.getElementById(`phone-text-${id}`).innerText = phone;
              document.getElementById(`email-text-${id}`).innerText = email;

              ["name","roll","phone","email"].forEach(field=>{
                  document.getElementById(`${field}-text-${id}`).style.display="inline";
                  document.getElementById(`${field}-input-${id}`).style.display="none";
              });
              document.getElementById("edit-btn-"+id).style.display="inline-block";
              document.getElementById("save-btn-"+id).style.display="none";
          } else {
              alert("Update failed! Check database connection.");
          }
      });
}
</script>
</body>
</html>