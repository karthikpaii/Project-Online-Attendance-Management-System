<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== "admin") {
    header("Location: sign.html");
    exit();
}

$college_code = $_SESSION['college_code'];

/* ===== AJAX DELETE ===== */
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = intval($_POST['id']);
    $conn = new mysqli("localhost", "root", "", "users");
    if ($conn->connect_error) die("Connection failed: ".$conn->connect_error);

    $stmt = $conn->prepare("DELETE FROM students WHERE id=? AND college_code=?");
    $stmt->bind_param("is", $id, $college_code);
    echo $stmt->execute() ? "success" : "error";
    $stmt->close();
    $conn->close();
    exit();
}

/* ===== AJAX UPDATE ===== */
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $rollno = $_POST['rollno'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    $conn = new mysqli("localhost", "root", "", "users");
    if ($conn->connect_error) die("Connection failed: ".$conn->connect_error);

    $stmt = $conn->prepare("UPDATE students SET name=?, rollno=?, phone=?, email=? WHERE id=? AND college_code=?");
    $stmt->bind_param("sssiis", $name, $rollno, $phone, $email, $id, $college_code);
    echo $stmt->execute() ? "success" : "error";
    $stmt->close();
    $conn->close();
    exit();
}

/* ===== FETCH STUDENTS ===== */
if (isset($_GET['batch']) && isset($_GET['class'])) {
    $batch_name = $_GET['batch'];
    $class_name = $_GET['class'];

    $conn = new mysqli("localhost", "root", "", "users");
    if ($conn->connect_error) die("Connection failed: ".$conn->connect_error);

    $stmt = $conn->prepare("SELECT * FROM students WHERE batch_name=? AND class_name=? AND college_code=? ORDER BY id ASC");
    $stmt->bind_param("sss", $batch_name, $class_name, $college_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        echo '<table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Roll No</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>';
        while($row = $result->fetch_assoc()){
            echo '<tr id="row-'.$row['id'].'">
                    <td>'.$row['id'].'</td>
                    <td><span id="name-text-'.$row['id'].'">'.htmlspecialchars($row['name']).'</span>
                        <input type="text" id="name-input-'.$row['id'].'" value="'.htmlspecialchars($row['name']).'" style="display:none;"></td>
                    <td><span id="roll-text-'.$row['id'].'">'.htmlspecialchars($row['rollno']).'</span>
                        <input type="text" id="roll-input-'.$row['id'].'" value="'.htmlspecialchars($row['rollno']).'" style="display:none;"></td>
                    <td><span id="phone-text-'.$row['id'].'">'.htmlspecialchars($row['phone']).'</span>
                        <input type="text" id="phone-input-'.$row['id'].'" value="'.htmlspecialchars($row['phone']).'" style="display:none;"></td>
                    <td><span id="email-text-'.$row['id'].'">'.htmlspecialchars($row['email']).'</span>
                        <input type="text" id="email-input-'.$row['id'].'" value="'.htmlspecialchars($row['email']).'" style="display:none;"></td>
                    <td class="action-button">
                        <button class="btn" id="edit-btn-'.$row['id'].'" onclick="editRow('.$row['id'].')">Edit</button>
                        <button class="btn" id="save-btn-'.$row['id'].'" onclick="saveRow('.$row['id'].')" style="display:none;">Save</button>
                        <button class="btn" onclick="deleteStudent('.$row['id'].')">Delete</button>
                    </td>
                  </tr>';
        }
        echo '</table>';
    } else {
        echo "<p>No students found for this batch and class.</p>";
    }
    $stmt->close();
    $conn->close();
    exit();
}

/* ===== FETCH BATCHES AND CLASSES ===== */
$conn = new mysqli("localhost", "root", "", "users");
if ($conn->connect_error) die("Connection failed: ".$conn->connect_error);

$batch_result = $conn->query("SELECT DISTINCT batch_name FROM batches WHERE college_code='$college_code' ORDER BY batch_name ASC");
$class_result = $conn->query("SELECT DISTINCT class_name FROM batches WHERE college_code='$college_code' ORDER BY class_name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Students</title>
    <style>
        body { font-family: Arial; background: #e8f0f2; margin:0; padding:20px;}
        h2 { text-align:center; margin-bottom:20px;}
        select, input[type=submit] { padding:8px; border-radius:5px; border:1px solid #ccc; margin-right:10px; font-size:14px;}
        input[type=submit] { cursor:pointer; background:#007bff; color:#fff; border:none; }
        input[type=submit]:hover { background:#0056b3; }
        table { width:100%; border-collapse:collapse; margin-top:20px;}
        th, td { border:1px solid #ddd; padding:10px; text-align:center;}
        th { background:#007bff; color:#fff;}
        .action-button { display:flex; justify-content:center; gap:10px;}
        .btn { padding:6px 10px; border:none; border-radius:5px; cursor:pointer; color:#fff; background:#007bff;}
        .btn:hover { background:#0056b3;}
        .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;}
        .modal-content { background:#fff; width:300px; margin:15% auto; padding:20px; border-radius:10px; text-align:center;}
        .modal-buttons { display:flex; justify-content:space-around; margin-top:15px;}
        .yes-btn { background:#dc3545; color:#fff; padding:8px 15px; border:none; border-radius:5px; cursor:pointer;}
        .no-btn { background:#6c757d; color:#fff; padding:8px 15px; border:none; border-radius:5px; cursor:pointer;}
    </style>
</head>
<body>
<h2>View Students</h2>

<form id="batchForm">
    <label>Batch:</label>
    <select name="batch" id="batchSelect" required>
        <option value="">-- Select Batch --</option>
        <?php while($b = $batch_result->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($b['batch_name']) ?>"><?= htmlspecialchars($b['batch_name']) ?></option>
        <?php endwhile; ?>
    </select>

    <label>Class:</label>
    <select name="class" id="classSelect" required>
        <option value="">-- Select Class --</option>
        <?php while($c = $class_result->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($c['class_name']) ?>"><?= htmlspecialchars($c['class_name']) ?></option>
        <?php endwhile; ?>
    </select>

    <input type="submit" value="View Students">
</form>

<div id="studentContainer"></div>

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

document.getElementById("batchForm").addEventListener("submit", function(e){
    e.preventDefault();
    const batch = document.getElementById("batchSelect").value;
    const cls = document.getElementById("classSelect").value;
    if(batch === "" || cls === "") return;

    fetch("?batch=" + encodeURIComponent(batch) + "&class=" + encodeURIComponent(cls))
    .then(res=>res.text())
    .then(html=>{
        document.getElementById("studentContainer").innerHTML = html;
    });
});

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

function editRow(id){
    ["name","roll","phone","email"].forEach(field=>{
        document.getElementById(`${field}-text-${id}`).style.display="none";
        document.getElementById(`${field}-input-${id}`).style.display="inline";
    });
    document.getElementById("edit-btn-"+id).style.display="none";
    document.getElementById("save-btn-"+id).style.display="inline";
}

function saveRow(id){
    let data = ["name","roll","phone","email"].reduce((obj, field)=>{
        obj[field] = encodeURIComponent(document.getElementById(`${field}-input-${id}`).value);
        return obj;
    }, {});

    fetch("",{
        method:"POST",
        headers: {"Content-Type":"application/x-www-form-urlencoded"},
        body:`action=update&id=${id}&name=${data.name}&rollno=${data.roll}&phone=${data.phone}&email=${data.email}`
    }).then(res=>res.text())
      .then(resp=>{
          if(resp.trim()==="success"){
              ["name","roll","phone","email"].forEach(field=>{
                  document.getElementById(`${field}-text-${id}`).innerText = decodeURIComponent(data[field]);
                  document.getElementById(`${field}-text-${id}`).style.display="inline";
                  document.getElementById(`${field}-input-${id}`).style.display="none";
              });
              document.getElementById("edit-btn-"+id).style.display="inline";
              document.getElementById("save-btn-"+id).style.display="none";
          } else alert("Update failed!");
      });
}
</script>
</body>
</html>