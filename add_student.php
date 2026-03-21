
<style>
h2
{
text-align:center;
font-size:40px;
color:black;
font-family: monospace;
text-shadow:3px 3px 5px #435353;

}
.forms{
    padding-left:60px;
}
.form-containes
{
        
        background-color: #ffffff;
        padding: 10px;
        width:400px;
        border:2px solid black;
        border-radius: 8px;
        box-shadow: 3px 3px 5px black;
        margin-bottom: 30px;   
}
form input[type="submit"] {

outline: none;
padding: 5px;
background:rgb(231, 6, 6);
color: #fff;
font-size: 14px;
cursor: pointer;
text-transform: uppercase;
transition: all 0.2s ease;
width: 120px;
border-radius: 5px;
}

form input[type="submit"]:hover {
    background: brown;
}
</style>  

<h2  class="add">Add Student</h2>
<?php
$conn = new mysqli("localhost", "root", "", "users");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch unique batch names and class names
$batch_result = $conn->query("SELECT DISTINCT batch_name FROM batches");
$class_result = $conn->query("SELECT DISTINCT class_name FROM batches");
?>


<?php if (isset($_GET['success'])): ?>
<?php endif; ?>



<div class="forms">
<div class="form-containes">
<form method="post" action="insert_student.php" id="studentForm">

    <label>Roll Number:</label><br><br>
    <input type="text" name="rollno" placeholder="Enter Roll Number" required><br><br>

    <label>Student Name:</label><br><br>
    <input type="text" name="student_name" placeholder="Enter Student Name" required><br><br>

    <!-- Batch dropdown -->
    <select name="batch" id="batch" required>
        <option value="">--Select Batch--</option>
        <?php while ($row = $batch_result->fetch_assoc()): ?> <!--Queries all unique batch_name values from the batches table-->
            <option value="<?= htmlspecialchars($row['batch_name']) ?>"><?= htmlspecialchars($row['batch_name']) ?></option> <!---etermines what data will be sent to the server when the form is submitted. and other is displayed to user--->
        <?php endwhile; ?> 
    </select><br><br>

    <!-- Class dropdown -->
    <select name="class" id="class" required>
        <option value="">--Select Class--</option>
    </select><br><br>

    <label>Enter Phone Number (Including +91): </label><br><br>
    <input type="text" name="phone" pattern="^\+91\d{10}$"  placeholder="Enter Phone Number " required><br><br>

    <label>Enter Email Id :</label><br><br>
    <input type="email" name="semail" placeholder="Enter Email Id" required><br><br>

    <input type="submit" value="Add Student">
</form>
</div>
    </div>
    
<script>
document.getElementById('batch').addEventListener('change', function () {
    const batch = this.value;  //This gets the value of the selected batch from the batch dropdown. 

    // Clear class dropdown
    const classSelect = document.getElementById('class');
    classSelect.innerHTML = '<option value="">--Select Class--</option>'; // This ensures that the dropdown is reset whenever a new batch is selected.

    if (!batch) return;

    fetch('get_classes.php?batch=' + encodeURIComponent(batch)) //encodeURIComponent() is used to   ensures special characters in the batch name are safely included in the URL.
        .then(response => response.json())  //fetches the classes returns a JavaScript object or array from the JSON sent by the server.
        .then(data => { //data is array of class name
            if (Array.isArray(data)) { // check data is an array
                data.forEach(className => { //This loop runs for each item in the data array.
                    const option = document.createElement('option'); // creates a new <option> HTML element.
                    option.value = className; //sets the value option
                    option.textContent = className; //sets what the user sees in the dropdown.
                    classSelect.appendChild(option); //adds this new option to the <select id="class">
                });
            }
        })
        .catch(error => {
            console.error('Error fetching classes:', error);
        });
});
</script>



<!--Creates a new <option> element. Sets its value and display text to the class name. Appends it to the class dropdown (<select id="class">).-->