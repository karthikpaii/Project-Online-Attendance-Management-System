<?php  
session_start();

if (!isset($_SESSION['user']) || !isset($_SESSION['college_code'])) {
    header("Location: sign.html");
    exit();
}


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


    $editData=null;

    if(isset($_GET['edit']))
        {
            $id=$_GET['edit'];

            $conn=new mysqli("localhost","root","","users");


            $stmt= $conn->prepare("SELECT * FROM batches WHERE id=?");
            $stmt->bind_param("i",$id);
            $stmt->execute();
            $result = $stmt->get_result();
            $editData = $result->fetch_assoc();
            $stmt->close();
            $conn->close();
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
        background:#28a745;
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

    .btn:hover {
        background: #0056b3;
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
    unset($_SESSION['success']);
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
            echo "<tr id='row-{$row['id']}'>
                    <td>{$row['id']}</td>
                    <td>{$row['batch_name']}</td>
                    <td>{$row['classes']}</td>
                    <td>{$row['year']}</td>
                    <td>
                        <button onclick='deleteBatch({$row['id']})'>Delete</button>
                        <button onclick='updateBatch({$row['id']})'> Update</button>
                    </td>
                  </tr>";
        }
        $conn->close();
        ?>
    </table>
</div>

<script>
    // function deleteBatch(id)
    // {
    //     if(!confirm("Do You Want To Delete?")) return;


    //     fetch("add_batch.php",
    //         {
    //             method: "POST",
    //             headers: {
    //                "Content-Type": "application/x-www-form-urlencoded" 
    //             },
    //             body: "action=delete&id=" + id
    //         })

    //         .then(res=>res.text())
    //         .then(data=>{
    //             document.getElementById("row-" + id).remove();
    //         });
    // }


//     function deleteBatch(id) {
//     if (!confirm("Do You Want To Delete?")) return;

//     fetch("add_batch.php", {
//         method: "POST",
//         headers: {
//             "Content-Type": "application/x-www-form-urlencoded"
//         },
//         body: "action=delete&id=" + id
//     })
//     .then(res => res.text())
//     .then(data => {
//         if (data === "success") {
//             document.getElementById("row-" + id).remove();
//         } else {
//             alert("Delete failed from database!");
//         }
//     });
// }
</script>

</body>
</html>