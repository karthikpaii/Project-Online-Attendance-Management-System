<?php
session_start();

if (!isset($_SESSION['college_code'])) {
    die("Session expired. Please login again.");
}

$college_code = $_SESSION['college_code'];

$conn = new mysqli("localhost", "root", "", "users");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* ================= AJAX: LOAD CLASSES ================= */
if (isset($_GET['batch'])) {
    header('Content-Type: application/json');

    $batch = $_GET['batch'];

    $stmt = $conn->prepare("SELECT classes FROM batches WHERE batch_name = ? AND college_code = ?");
    $stmt->bind_param("ss", $batch, $college_code);
    $stmt->execute();
    $result = $stmt->get_result();

    $classes = [];
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row['classes'];
    }

    echo json_encode($classes);
    exit();
}
?>

<!-- ================= UI ================= -->

<style>
.form-box {
    background-color: #ffffff;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 30px;
}
.form-box h2 {
    text-align: center;
}
.form-box input,
.form-box select {
    padding: 10px;
    margin-bottom: 10px;
}
.form-row {
    display: flex;
    gap: 10px;
}
.container {
    width: 80%;
    margin: 50px auto;
}
</style>

<div class="container">
<div class="form-box">

<h2>View Student List</h2>

<form id="view-student-form">
<div class="form-row">

<label>Select Batch:</label>
<select name="batch" id="batchSelect" required>
    <option value="">-- Select Batch --</option>

<?php
$stmt = $conn->prepare("SELECT DISTINCT batch_name FROM batches WHERE college_code = ?");
$stmt->bind_param("s", $college_code);
$stmt->execute();
$result = $stmt->get_result();

while ($b = $result->fetch_assoc()) {
    echo "<option value='" . htmlspecialchars($b['batch_name']) . "'>" . htmlspecialchars($b['batch_name']) . "</option>";
}
$stmt->close();
?>

</select>

<label>Select Class:</label>
<select name="class" id="classSelect" required>
    <option value="">-- Select Class --</option>
</select>

<input type="submit" value="View Students">

</div>
</form>

</div>
</div>

<div id="student-list"></div>

<!-- ================= JS ================= -->

<script>
// ✅ Load classes when batch changes
document.getElementById('batchSelect').addEventListener('change', function () {

    const batch = this.value;
    const classSelect = document.getElementById('classSelect');

    classSelect.innerHTML = '<option value="">-- Select Class --</option>';

    if (!batch) return;

    fetch('?batch=' + encodeURIComponent(batch))   // ✅ SAME FILE CALL
        .then(res => res.json())
        .then(data => {
            data.forEach(c => {
                let option = document.createElement('option');
                option.value = c;
                option.textContent = c;
                classSelect.appendChild(option);
            });
        })
        .catch(err => console.error("Class load error:", err));
});


// ✅ Load students
document.getElementById('view-student-form').addEventListener('submit', function (e) {

    e.preventDefault();

    const batch = document.getElementById('batchSelect').value;
    const className = document.getElementById('classSelect').value;

    fetch('view_student.php?batch=' + encodeURIComponent(batch) + '&class=' + encodeURIComponent(className))
        .then(res => res.text())
        .then(html => {
            document.getElementById('student-list').innerHTML = html;
        })
        .catch(err => console.error("Student load error:", err));
});
</script>