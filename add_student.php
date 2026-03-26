<?php
session_start();

// ✅ SESSION CHECK
if (!isset($_SESSION['college_code'])) {
    die("Session expired. Please login again.");
}
$college_code = $_SESSION['college_code'];


// ✅ DB CONNECTION (ONLY ONCE)
$conn = new mysqli("localhost", "root", "", "users");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ AJAX HANDLER (MUST BE AT TOP)
if (isset($_GET['batch'])) {
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



$batch_result = $conn->prepare("SELECT DISTINCT batch_name FROM batches WHERE college_code = ?");
$batch_result->bind_param("s", $college_code);
$batch_result->execute();
$batch_result = $batch_result->get_result();


// ✅ INSERT LOGIC
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $rollno = trim($_POST['rollno']);
    $student_name = trim($_POST['student_name']);
    $batch = trim($_POST['batch']);
    $class = trim($_POST['class']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['semail']);
    $dob = trim($_POST['dob']);

    if (!empty($rollno) && !empty($student_name) && !empty($batch) && !empty($class) && !empty($phone) && !empty($email) && !empty($dob)) {

        $check = $conn->prepare("SELECT * FROM students WHERE rollno = ? AND  college_code=?");
        $check->bind_param("ss", $rollno,$college_code);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('❌ Roll number already exists!');</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO students (rollno, name, batch_name, class_name, phone, email, dob, college_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $rollno, $student_name, $batch, $class, $phone, $email, $dob, $college_code);

            if ($stmt->execute()) {
                echo "<script>alert('✅ Student added successfully!');</script>";
            } else {
    if ($stmt->errno == 1062) {
        echo "<script>alert('❌ Roll number already exists in this batch & class!');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
 $stmt->close();
    }

    $check->close();

} else {
    echo "<script>alert('⚠️ All fields are required.');</script>";
}
}

?>

<!-- ================= HTML START ================= -->

<style>
h2 {
    text-align:center;
    font-size:40px;
    font-family: monospace;
    text-shadow:3px 3px 5px #435353;
}
  .form-group {
        flex: 1;
        min-width: 200px;
        display: flex;
        flex-direction: column;
    }

    label {
        margin-bottom: 5px;
        font-weight: bold;
    }

     .form-row {
        display: flex;
        gap: 20px;
        flex-wrap: wrap; /* responsive */
        margin-bottom: 15px;
    }


    input, select {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }  

    .success
    {
       text-align:center;
       padding:10px;
    }

    .error
    {
        background:#dc3545;
    }

    .btn {
        display: block;
        width: 100px;
        margin: 20px auto 0;
        padding: 10px;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
  .btn:hover {
        background: #0056b3;
    }

    .container{
    width: 80%;
    margin: 30px auto;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}


</style>

<h2>Add Student</h2>


<div class="container">
<form method="post">
<div class="form-row">
            <div class="form-group">
    <label>Roll Number:</label><br>
    <input type="text" name="rollno" required><br><br>
</div>

    <div class="form-group">
    <label>Student Name:</label><br>
    <input type="text" name="student_name" required><br><br>
  </div>
    <div class="form-group">
     <label> Select Batch </label>
    <select name="batch" id="batch" required>
        <option value="">--Select Batch--</option>
        <?php while ($row = $batch_result->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($row['batch_name']) ?>">
                <?= htmlspecialchars($row['batch_name']) ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>
  </div>
 <div class="form-group">
   <label> Select Class </label>
    <select name="class" id="class" required>
        <option value="">--Select Class--</option>
    </select><br><br>
  </div>
    <div class="form-group">
    <label>Phone Number</label>
    <input type="text" name="phone" placeholder="+91XXXXXXXXXX" pattern="^\+91[6-9]\d{9}$" required><br><br>
     </div>
      <div class="form-group">
    <label>Email ID :</label>
    <input type="email" name="semail" placeholder="Email" required><br><br>
      </div>
    <div class="form-group">
    <label> Date Of Birth:</label>
    <input type="date" name="dob" ><br><br>
        </div>

        <br>
    <input type="submit" class="btn"  value="Add Student">

</form>

</div>

<script>
document.getElementById('batch').addEventListener('change', function () {

    const batch = this.value;
    const classSelect = document.getElementById('class');

    classSelect.innerHTML = '<option value="">--Select Class--</option>';

    if (!batch) return;

    fetch('?batch=' + encodeURIComponent(batch))
        .then(res => res.json())
        .then(data => {
            data.forEach(c => {
                let option = document.createElement('option');
                option.value = c;
                option.textContent = c;
                classSelect.appendChild(option);
            });
        })
        .catch(err => console.error(err));
});
</script>