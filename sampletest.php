<style>
body { font-family: Arial, sans-serif; background: #e8f0f2; margin:0; padding:20px;}
h2 { text-align:center; color: #333;}
table { width:100%; border-collapse:collapse; margin-top:10px; background:#fff;}
th, td { border:1px solid #ddd; padding:12px; text-align:center;}
th { background:#007bff; color:#fff;}
.btn { padding:6px 12px; border:none; border-radius:4px; cursor:pointer; color:#fff; background:#007bff;}
</style>

<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user']) || $_SESSION['role'] !== "admin") {
    header("Location: sign.html");
    exit();
}

$college_code = $_SESSION['college_code'];
$conn = new mysqli("localhost","root","","users");


/* ===== LOAD CLASSES ===== */
if (isset($_GET['get_classes_for_batch'])) {

    $batch = $_GET['get_classes_for_batch'];

    $stmt = $conn->prepare("SELECT DISTINCT classes FROM batches WHERE batch_name=? AND college_code=?");
    $stmt->bind_param("ss", $batch, $college_code);
    $stmt->execute();
    $res = $stmt->get_result();

    echo '<option value="">-- Select Class --</option>';

    while($row = $res->fetch_assoc()){
        echo '<option value="'.$row['classes'].'">'.$row['classes'].'</option>';
    }

    exit();
}


/* ===== UPDATE ===== */
if (isset($_POST['action']) && $_POST['action'] === 'update') {

    $id = intval($_POST['id']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE attendance SET status=? WHERE id=? AND college_code=?");

    if (!$stmt) {
        echo "error";
        exit();
    }

    $stmt->bind_param("sis", $status, $id, $college_code);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    exit();
}


/* ===== DELETE ===== */
if (isset($_POST['action']) && $_POST['action'] === 'delete') {

    $id = intval($_POST['id']);

    $stmt = $conn->prepare("DELETE FROM attendance WHERE id=? AND college_code=?");

    if (!$stmt) {
        echo "error";
        exit();
    }

    $stmt->bind_param("is", $id, $college_code);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo "success";
    } else {
        echo "error";
    }

    exit();
}

/* ===== LOAD STUDENTS ===== */
if (isset($_GET['batch']) && isset($_GET['class'])) {

    $batch = $_GET['batch'];
    $class = $_GET['class'];

    $stmt = $conn->prepare("SELECT * FROM attendance WHERE batch_name=? AND class_name=? AND college_code=?");
    $stmt->bind_param("sss", $batch, $class, $college_code);
    $stmt->execute();
    $res = $stmt->get_result();

    echo '<table id="studentTable">
    <tr>
        <th>Name</th>
        <th>Roll</th>
        <th>Status</th>
        <th>Action</th>
    </tr>';

    while($row = $res->fetch_assoc()){
        $id = $row['id'];

        echo '<tr id="row-'.$id.'">
            <td>'.$row['student_name'].'</td>
            <td>'.$row['student_roll'].'</td>

            <td>
                <span id="status-text-'.$id.'">'.$row['status'].'</span>

                <select id="status-input-'.$id.'" style="display:none;">
                    <option value="Present" '.($row['status']=="Present"?"selected":"").'>Present</option>
                    <option value="Absent" '.($row['status']=="Absent"?"selected":"").'>Absent</option>
                </select>
            </td>

           <td>
    <button class="btn" id="edit-btn-'.$id.'" onclick="editRow('.$id.')">Edit</button>

    <button class="btn" id="save-btn-'.$id.'" onclick="saveRow('.$id.')" 
        style="display:none;background:green;">Save</button>

    <button class="btn" style="background:#dc3545;" 
        onclick="deleteStudent('.$id.')">Delete</button>
</td>
        </tr>';
    }

    echo '</table>';
    exit();
}


/* ===== LOAD BATCHES ===== */
$batch_result = $conn->query("SELECT DISTINCT batch_name FROM batches WHERE college_code='$college_code'");
?>

<!DOCTYPE html>
<html>
<body>

<h2>Student Management</h2>

<form id="batchForm">
    <label>Batch:</label>
    <select id="batchSelect" required>
        <option value="">-- Select Batch --</option>
        <?php while ($b = $batch_result->fetch_assoc()): ?>
            <option value="<?= $b['batch_name'] ?>"><?= $b['batch_name'] ?></option>
        <?php endwhile; ?>
    </select>

    <label>Class:</label>
    <select id="classSelect" required>
        <option value="">-- Select Batch First --</option>
    </select>

    <button type="submit">Load Data</button>
</form>

<div id="data"></div>

<script>

// LOAD CLASSES
document.getElementById("batchSelect").addEventListener("change", function () {

    let batch = this.value;

    fetch("?get_classes_for_batch=" + encodeURIComponent(batch))
    .then(res => res.text())
    .then(data => {
        document.getElementById("classSelect").innerHTML = data;
    });
});


// LOAD STUDENTS
document.getElementById("batchForm").addEventListener("submit", function (e) {
    e.preventDefault();

    let batch = document.getElementById("batchSelect").value;
    let cls = document.getElementById("classSelect").value;

    fetch(`?batch=${batch}&class=${cls}`)
    .then(res => res.text())
    .then(data => {
        document.getElementById("data").innerHTML = data;
    });
});


// EDIT
function editRow(id){
    document.getElementById(`status-text-${id}`).style.display="none";
    document.getElementById(`status-input-${id}`).style.display="inline";

    document.getElementById(`edit-btn-${id}`).style.display="none";
    document.getElementById(`save-btn-${id}`).style.display="inline";
}


// SAVE
function saveRow(id){

    let status = document.getElementById(`status-input-${id}`).value;

    let params = new URLSearchParams();
    params.append("action","update");
    params.append("id",id);
    params.append("status",status);

    fetch("",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:params.toString()
    })
    .then(res=>res.text())
    .then(resp=>{
        
        console.log("SERVER:", resp);

        if(resp.includes("success")){

            document.getElementById(`status-text-${id}`).innerText = status;

            document.getElementById(`status-text-${id}`).style.display="inline";
            document.getElementById(`status-input-${id}`).style.display="none";

            document.getElementById(`edit-btn-${id}`).style.display="inline";
            document.getElementById(`save-btn-${id}`).style.display="none";

        } else {
            alert("Update failed!");
        }
    });
}

// DELETE
function deleteStudent(id){

    if(!confirm("Are you sure you want to delete?")) return;

    fetch("",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:`action=delete&id=${id}`
    })
    .then(res=>res.text())
    .then(resp=>{

        console.log("DELETE:", resp);

        if(resp.includes("success")){
            let row = document.getElementById("row-"+id);
            if(row) row.remove();
        } else {
            alert("Delete failed!");
        }
    });
}


</script>

</body>
</html>