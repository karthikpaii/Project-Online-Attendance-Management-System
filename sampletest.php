
<style>
        body { font-family: Arial, sans-serif; background: #e8f0f2; margin:0; padding:20px;}
        h2 { text-align:center; color: #333;}
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
<?php
session_start();
// SECURITY CHECK
if (!isset($_SESSION['user']) || $_SESSION['role'] !== "admin") {
    header("Location: sign.html");
    exit();
}

$college_code = $_SESSION['college_code'];
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "users";

/* ===== AJAX: FETCH CLASSES ===== */
if (isset($_GET['get_classes_for_batch'])) {
    $batch = $_GET['get_classes_for_batch'];
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    $stmt = $conn->prepare("SELECT DISTINCT classes FROM batches WHERE batch_name=? AND college_code=? ORDER BY classes ASC");
    $stmt->bind_param("ss", $batch, $college_code);
    $stmt->execute();
    $res = $stmt->get_result();

    echo '<option value="">-- Select Class --</option>';
    while ($row = $res->fetch_assoc()) {
        echo '<option value="'.htmlspecialchars($row['classes']).'">'.htmlspecialchars($row['classes']).'</option>';
    }

    $stmt->close();
    $conn->close();
    exit(); //
}

if (isset($_POST['action']) && $_POST['action'] === 'update') {

    $id = intval($_POST['id']);
    $status = $_POST['status'];

    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    // ✅ CHECK PREPARE FIRST
    $stmt = $conn->prepare("UPDATE attendance SET status=? WHERE id=? AND college_code=?");

    if (!$stmt) {
        echo "prepare_error: " . $conn->error;
        exit();
    }

    $stmt->bind_param("sis", $status, $id, $college_code);

    if (!$stmt->execute()) {
        echo "execute_error: " . $stmt->error;
        exit();
    }

    // ✅ HANDLE BOTH CASES
    if ($stmt->affected_rows >= 0) {
        echo "success";   // even if same value
    } else {
        echo "failed";
    }

    $stmt->close();
    $conn->close();
    exit();
}

/* ===== AJAX: FETCH STUDENT TABLE ===== */
if (isset($_GET['batch']) && isset($_GET['class'])) {
    $batch_name = $_GET['batch'];
    $class_name = $_GET['class'];

    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    $stmt = $conn->prepare("SELECT * FROM attendance WHERE batch_name=? AND class_name=? AND college_code=? ORDER BY id ASC");
    $stmt->bind_param("sss", $batch_name, $class_name, $college_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        echo '<div style="margin-bottom:10px;">
                <input type="text" id="tableSearch" onkeyup="filterTable()" 
                placeholder="Search..." 
                style="width:100%; padding:10px;">
              </div>';

        echo '<h2>Student List</h2>';

        // ✅ FIXED ID
        echo '<table id="studentTable">
                <tr>
                    <th>Name</th>
                    <th>Roll Number</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>';

        while ($row = $result->fetch_assoc()) {
            $sid = $row['id'];
            echo '<tr>
                    <td> '. htmlspecialchars($row['student_name']).' </td>
                    <td>'. htmlspecialchars($row['student_roll']) .' </td>
                    <td><span id="status-text-'.$sid.'">'.htmlspecialchars($row['status']).'</span>
                        <select id="status-input-'.$sid.'" style="display:none;">
    <option value="Present" '.($row['status']=="Present"?"selected":"").'>Present</option>
    <option value="Absent" '.($row['status']=="Absent"?"selected":"").'>Absent</option>
</select>
                  <td class="action-button">
                        <button class="btn" id="edit-btn-'.$sid.'" onclick="editRow('.$sid.')">Edit</button>
                        <button class="btn" id="save-btn-'.$sid.'" onclick="saveRow('.$sid.')" style="display:none; background:#28a745;">Save</button>
                        <button class="btn" style="background:#dc3545;" onclick="deleteStudent('.$sid.')">Delete</button>
                    </td>
                    </tr>';
        }

        echo '</table>';
    } else {
        echo "<p>No records found</p>";
    }

    $stmt->close();
    $conn->close();

    exit();
}

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
$batch_result = $conn->query("SELECT DISTINCT batch_name FROM batches WHERE college_code='$college_code'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Management</title>
</head>
<body>

<h2>Student Management Portal</h2>

<form id="batchForm">
    <label>Batch:</label>
    <select id="batchSelect" required>
        <option value="">-- Select Batch --</option>
        <?php while ($b = $batch_result->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($b['batch_name']) ?>">
                <?= htmlspecialchars($b['batch_name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Class:</label>
    <select id="classSelect" required>
        <option value="">-- Select Batch First --</option>
    </select>

    <button type="submit">Load Data</button>
</form>

<hr>

<div id="studentContainer"></div>

<script>
// LOAD CLASSES
document.getElementById("batchSelect").addEventListener("change", function () {
    const batch = this.value;

    fetch("?get_classes_for_batch=" + encodeURIComponent(batch))
        .then(res => res.text())
        .then(data => {
            document.getElementById("classSelect").innerHTML = data;
        });
});

// LOAD STUDENTS
document.getElementById("batchForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const batch = document.getElementById("batchSelect").value;
    const cls = document.getElementById("classSelect").value;

    fetch("?batch=" + encodeURIComponent(batch) + "&class=" + encodeURIComponent(cls))
        .then(res => res.text())
        .then(data => {
            document.getElementById("studentContainer").innerHTML = data;
        });
});

// SEARCH FILTER
function filterTable() {
    const input = document.getElementById("tableSearch").value.toUpperCase();
    const rows = document.querySelectorAll("#studentTable tr");

    rows.forEach((row, index) => {
        if (index === 0) return;
        row.style.display = row.innerText.toUpperCase().includes(input) ? "" : "none";
    });
}



// EDIT/SAVE LOGIC
function editRow(id){
    ["status"].forEach(field=>{
        document.getElementById(`${field}-text-${id}`).style.display="none";
        document.getElementById(`${field}-input-${id}`).style.display="inline-block";
        document.getElementById(`${field}-input-${id}`).style.width="90%";
    });
    document.getElementById("edit-btn-"+id).style.display="none";
    document.getElementById("save-btn-"+id).style.display="inline-block";
}

function saveRow(id){
    const status = document.getElementById(`status-input-${id}`).value;

    const params = new URLSearchParams();
    params.append('action', 'update');
    params.append('id', id);          // ✅ THIS LINE WAS MISSING
    params.append('status', status);

    fetch("",{
        method:"POST",
        headers: {"Content-Type":"application/x-www-form-urlencoded"},
        body: params.toString()
    })
    .then(res=>res.text())
    .then(resp=>{
    
        if(resp.trim()==="success"){
            document.getElementById(`status-text-${id}`).innerText = status;

            document.getElementById(`status-text-${id}`).style.display="inline";
            document.getElementById(`status-input-${id}`).style.display="none";

            document.getElementById("edit-btn-"+id).style.display="inline-block";
            document.getElementById("save-btn-"+id).style.display="none";
        } else {
            alert("Update failed!");
        }
    });
}

</script>

</body>
</html>