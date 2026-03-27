<style>
 table 
 { 
    width:100%; 
    border-collapse:collapse; 
    margin-top:10px; 
    background:#fff;
}

 th, td { 
    border:1px solid #ddd; 
    padding:12px; 
    text-align:center;
}
        
    th { 
        background:#007bff; 
    color:#fff;
}

h2
{
    text-align:center;
}
</style>
<?php
session_start();

if(!isset($_SESSION['college_code']))
    {
        die("Session Expired. Please Login Again");
    }

    $college_code=$_SESSION['college_code'];


    $conn=new mysqli("localhost","root","","users");

    if($conn->connect_error)
        {
            die("Connection Failed".$conn->connect_error);
        }

$stmt=$conn->prepare("SELECT name, role, date_joined  FROM login WHERE college_code=?");
$stmt->bind_param("s",$college_code);
$stmt->execute();
$result=$stmt->get_result();

if($result->num_rows>0)
    {
        echo '<h2>Admin List </h2>';
        echo '<table id="adminTable">
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Date Joined </td>
                </tr>';
        while($row=$result->fetch_assoc())
            {
                 echo "<tr> 
                 <td>" .htmlspecialchars($row['name']). "</td>
                 <td>" .htmlspecialchars($row['role']). "</td>
                 <td>" .htmlspecialchars($row['date_joined']). "</td>
                 </tr>";

            }
            echo '</table>';
    } else{
        echo "No Admin Details found";
    }

    $stmt->close();
    $conn->close();


?>