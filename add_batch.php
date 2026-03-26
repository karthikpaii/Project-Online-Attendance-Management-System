<?php  
session_start();

if (!isset($_SESSION['user']) || !isset($_SESSION['college_code'])) {
    header("Location: sign.html");
    exit();
}?>


<?php

if (isset($_POST['action']) && $_POST['action'] == 'delete') {

    $id = $_POST['id'];

    $conn = new mysqli("localhost", "root", "", "users");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("DELETE FROM batches WHERE id = ?");
    $stmt->bind_param("i", $id); //i used -> it means $id is a type of int 

    if ($stmt->execute()) {
        echo "success"; 
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
    exit(); 
}
?>


<?php
if(isset($_POST['action']) && $_POST['action']=='update')
    {
        $id=$_POST['id'];
        $batch_name=$_POST['batch_name'];
        $classes=$_POST['classes'];
        $year=$_POST['year'];


        $conn=new mysqli("localhost","root","","users");

        if($conn->connect_error)
            {
                die("Conntection Failed". $conn->connect_error);
            }


         $stmt=$conn->prepare("UPDATE batches SET batch_name=?, classes=?, year=? WHERE id=?");
         $stmt->bind_param("sssi",$batch_name,$classes,$year,$id); //batch name is s {string}, clss and year is string and id is int
         
         if($stmt->execute())
            {
                echo "success";

            }else {

            echo "Error";
            }

            $stmt->close();
            $conn->close();
            exit();
    }
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $batch_name = trim($_POST["batch_name"]);
    $classes = trim($_POST["classes"]);
    $year = trim($_POST["year"]);
    $college_code = $_SESSION['college_code'];

   
    if (empty($batch_name) || empty($classes) || empty($year)) {
        $_SESSION['error'] = "All fields are required!";
    } else {

      
        $conn = new mysqli("localhost", "root", "", "users");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        
        $stmt = $conn->prepare("INSERT INTO batches (batch_name, classes, year, college_code) VALUES (?, ?, ?, ?)");

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        
        $stmt->bind_param("ssss", $batch_name, $classes, $year, $college_code);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Batch added successfully!";
        } else {
            $_SESSION['error'] = "Error adding batch!";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Batch</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background:  #e8f0f2;
        margin: 0;
        padding: 0;
        
    }

    .table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.table th, .table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
}

.table th {
    background: #007bff;
    color: white;
}

.container, .con {
    width: 80%;
    margin: 30px auto;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}


    h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    .form-row {
        display: flex;
        gap: 20px;
        flex-wrap: wrap; /* responsive */
        margin-bottom: 15px;
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
        width: 150px;
        margin: 20px auto 0;
        padding: 10px;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .action-button
    {
        display:flex;
        gap:10px;
        justify-content:center;
    }

        .delete-btn, .update-btn {
       padding:6px 10px;
        
      

        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .btn:hover {
        background: #0056b3;
    }

    .modal {
    display: none;
    position: fixed;
    z-index: 999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
}

.modal-content {
    background: #fff;
    padding: 20px;
    width: 300px;
    margin: 15% auto;
    text-align: center;
    border-radius: 10px;
}

.modal-buttons {
    margin-top: 15px;
    display: flex;
    justify-content: space-around;
}

.yes-btn {
    background: #dc3545;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
}

.no-btn {
    background: #6c757d;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
}


</style>
</head>
<body>

<h2>Add New Batch</h2>
<div class="container">
    <h2>Add Batch</h2>

    <form method="POST" action="add_batch.php">

        <div class="form-row">
            <div class="form-group">
                <label>Batch Name</label>
                <input type="text" name="batch_name" required>
            </div>

            <div class="form-group">
                <label>Class/Course</label>
               <input type="text" name="classes" placeholder="e.g. BCA, BCOM" required>
            </div>


             <div class="form-group">
                <label>year</label>
                   <input type="text" name="year" placeholder="e.g. 2026-2027" required>
            </div>
        </div>
        <button type="submit" class="btn">Add Batch</button>

    </form>

<?php
if (isset($_SESSION['success'])) {
   echo "<div class='message success'>" . $_SESSION['success'] . "</div>";
    unset($_SESSION['success']); //unset Remove the message after showing it
}
if (isset($_SESSION['error'])) {
    echo "<div class='message error'>" . $_SESSION['error'] . "</div>";
    unset($_SESSION['error']);
}
?>
</div>

   <div class="con">  
    <?php
    $conn = new mysqli("localhost", "root", "", "users");
    $college_code = $_SESSION['college_code'];

    $result = $conn->query("SELECT * FROM batches WHERE college_code='$college_code' ORDER BY id DESC");
    ?>

    <h2 style="margin-top:30px; text-align:center;">Batch List</h2>

    <table class="table">
        <tr>
            <th>ID</th>
            <th>Batch Name</th>
            <th>Class</th>
            <th>Year</th>
            <th>Action</th>
        </tr>

        <?php
        while($row = $result->fetch_assoc()) {
            #name text normally displays text and name-input for updating [initially hidden]
            echo "<tr id='row-{$row['id']}'>
                    <td>{$row['id']}</td>

                    <td>
                    <span id='name-text-{$row['id']}'>{$row['batch_name']} </span>
                    <input type='text' id='name-input-{$row['id']}' value='{$row['batch_name']}' style='display:none;'>  
                    </td>


                    <td>
                    <span id='class-text-{$row['id']}'>{$row['classes']} </span>
                    <input type='text' id='class-input-{$row['id']}' value='{$row['classes']}' style='display:none;'>
                    </td>


                    <td>
                    <span id='year-text-{$row['id']}'>{$row['year']} </span>
                    <input type='text' id='year-input-{$row['id']}' value='{$row['year']}' style='display:none;'>
                    </td>

                    <td>
                          <div class='action-button'>
                          <button class='delete-btn' onclick='deleteBatch({$row['id']})'>Delete</button>

                          <button class='update-btn' onclick='editRow({$row['id']})' id='edit-btn-{$row['id']}'>Update</button>
                          <button class='update-btn' onclick='saveRow({$row['id']})' id='save-btn-{$row['id']}' style='display:none;'>Save</button>
                          </div>
                        </td>
                  </tr>";
        }
        $conn->close();
        ?>
    </table>
</div>

<div id="deleteModal" class="modal">
    <div class="modal-content">
        <p> Are You Sure you want to delete?</P>
        <div class="modal-buttons">
            <button onclick="confirmDelete()" class="yes-btn">Yes</button>
            <button onclick="closeModal()" class="no-btn">Cancel</button>
    </div>
</div>
</div>
<script>

let deleteId = null;

function deleteBatch(id) {
    deleteId = id;
    document.getElementById("deleteModal").style.display = "block";
}

function closeModal() {
    document.getElementById("deleteModal").style.display = "none";
}

function confirmDelete() {
    fetch("", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "action=delete&id=" + deleteId
    })
    .then(res => res.text())
    .then(data => {
        if (data.trim() === "success") {
            document.getElementById("row-" + deleteId).remove();
        } else {
            alert("Delete failed!");
        }
        closeModal();
    });
}



function editRow(id)
{
    document.getElementById("name-text-" + id).style.display="none";
    document.getElementById("class-text-" + id).style.display="none";
    document.getElementById("year-text-" + id).style.display="none";


    document.getElementById("name-input-" + id).style.display="inline";
    document.getElementById("class-input-" + id).style.display="inline";
    document.getElementById("year-input-" + id).style.display="inline";

    document.getElementById("edit-btn-" + id).style.display="none";
    document.getElementById("save-btn-" + id).style.display="inline";
}

function saveRow(id)
{
    let name=document.getElementById("name-input-" + id).value;
    let classes=document.getElementById("class-input-" + id).value;
    let year=document.getElementById("year-input-" + id).value;

    fetch("",{ //"" means same page [fetch() sends data to server without relaoding]
        method:"POST", //using post method sending data
        headers: {
            "Content-Type":"application/x-www-form-urlencoded" //format of  data
         },
         //sending data to server [& is seperator]
       body: "action=update&id=" + id + "&batch_name=" + encodeURIComponent(name) + "&classes=" + encodeURIComponent(classes) +  "&year=" + encodeURIComponent(year)
    })
    .then(res => res.text())
    .then(data => {
        if (data.trim() === "success") {

            // update UI
            document.getElementById("name-text-" + id).innerText = name;
            document.getElementById("class-text-" + id).innerText = classes;
            document.getElementById("year-text-" + id).innerText = year;

            // switch back
            document.getElementById("name-text-" + id).style.display = "inline";
            document.getElementById("class-text-" + id).style.display = "inline";
            document.getElementById("year-text-" + id).style.display = "inline";

            document.getElementById("name-input-" + id).style.display = "none";
            document.getElementById("class-input-" + id).style.display = "none";
            document.getElementById("year-input-" + id).style.display = "none";

            document.getElementById("edit-btn-" + id).style.display = "inline";
            document.getElementById("save-btn-" + id).style.display = "none";

        } else {
            alert("Update failed!");
        }
    });
}
</script>

</body>
</html>