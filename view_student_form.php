<!-- <!-- <?php
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
? -->

<!-- ================= JS ================= -->

<script>
// // ✅ Load classes when batch changes
// document.getElementById('batchSelect').addEventListener('change', function () {

//     const batch = this.value;
//     const classSelect = document.getElementById('classSelect');

//     classSelect.innerHTML = '<option value="">-- Select Class --</option>';

//     if (!batch) return;

//     fetch('?batch=' + encodeURIComponent(batch))   // ✅ SAME FILE CALL
//         .then(res => res.json())
//         .then(data => {
//             data.forEach(c => {
//                 let option = document.createElement('option');
//                 option.value = c;
//                 option.textContent = c;
//                 classSelect.appendChild(option);
//             });
//         })
//         .catch(err => console.error("Class load error:", err));
// });


// // ✅ Load students
// document.getElementById('view-student-form').addEventListener('submit', function (e) {

//     e.preventDefault();

//     const batch = document.getElementById('batchSelect').value;
//     const className = document.getElementById('classSelect').value;

//     fetch('view_student.php?batch=' + encodeURIComponent(batch) + '&class=' + encodeURIComponent(className))
//         .then(res => res.text())
//         .then(html => {
//             document.getElementById('student-list').innerHTML = html;
//         })
//         .catch(err => console.error("Student load error:", err));
// });
// </script>