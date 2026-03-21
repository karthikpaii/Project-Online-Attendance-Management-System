<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== "admin") {
    header("Location: sign.html");
    exit();
}

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width= , initial-scale=1.0">
    <title>Interface</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
*{
    text-decoration: none;
}
.navbar {

    background:#2c3e50;
    font-family: cursive;
    padding-right: 15px;
    padding-left: 15px;
    padding-bottom: 15px;
   
}
.navdiv {
    display: flex; align-items: center; justify-content: space-between;
}
.logo a {
    font-size: 20px; font-weight: 500; color: white;
}
.navdiv li {
    list-style: none; display: inline-block;
}
.navdiv li a {
    color: white;
    font-size: 18px;
    font-weight: bold;
    margin-right: 25px;
}
.navdiv li a:hover {
    color: skyblue;
    
}
#name{
    color:black;
}
.sidebar a i {
    margin-right: 10px; width: 20px; text-align: center;
}
.sidebar{
  position: fixed;
   width: 2200;
   height:100vh;
   background-color: #2c3e50;
   padding: 30px;
   color: white;
}
.sidebar a{
    display: block;
    color:white;
    padding: 10px 0;
    text-decoration: none;
    transition: 0.3s;
}
.sidebar a:hover{
    background-color: #34495e; border-radius: 5px; padding-left: 10px;
}
.sidebar h2{
    color: orange; font-size: 20px; margin-bottom: 30px;
}
.body
{
    margin: 0; font-family: Arial,sans-serif; background-color: #e8f0f2;
}
html, body { margin: 0; padding: 0; height: 100%; font-family: cursive; }
.sidebar .Logout {
margin-top: 20px;
background-color: #e74c3c;
padding: 10px;
border: none;
color: white;
cursor: pointer;
border-radius: 5px;
}
.sidebar .Logout:hover {background-color:none;}
.form-container {
    max-width: 400px;
    margin: 40px;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px #ccc;
    text-align: left;
    align-items:center;
}
.form-container h2{
    text-align: center; }

.batch-content h2 {
    padding-left: 5px;
}
.records {
    max-width: 400px;
    margin: 40px;
    padding: 20px;
    border-radius: 10px;

}
th,td { padding:10px;}

.success-message {
    color: green;
    font-weight: bold;
    background-color: #eaffea;
    border: 1px solid green;
    padding: 10px;
    margin: 20px auto;
    width: fit-content;
    max-width: 80%;
    text-align: center;
    border-radius: 8px;
}
.homec ul {
    list-style: none;
    display: flex;
    gap: 12px;
    padding: 0;
    margin: 0;
    flex-wrap: wrap;
    
}
.homec li {
    margin: 4px;
     padding-left:50px;
     align-items:center;
}

.homec li a {
    display: inline-block;
    padding: 10px 16px;
    color: white;
    text-decoration: none;
    font-weight: bold;
    border: 2px solid black;
    background-color: red;
    border-radius: 8px;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.homec li a:hover {
    background-color: darkred; color: #fff;
}
.welcome
{
    text-align:center;
}
#absentList {
  margin-top: 20px; 
}
#absentList table {
  margin-top: 20px;
}
#absentList { 
  margin-top: 20px;  padding-left:15px;
}
.cap
{
  font-size:25px; padding-bottom:20px;
}
#content-area {
    position: absolute;
    top: 75px;      /* below navbar */
    left: 0px;    /* beside sidebar */
    right: 0;
    bottom: 0;
    padding: 20px;
    overflow-y: auto;
    background-color: #e8f0f2;
}
h1 {
    text-align:center;
    font-size:30px;
    color:black;
    font-family: monospace;
    text-shadow:3px 3px 5px #435353;

}
</style>
</head>
<body>
<nav class="navbar">
        <div class="navdiv">
            <div class="logo"><a href="#">Online Attendance Recorder</a></div>
            <ul>
                <li><a href="home2.html">Home</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="privacy.html">Privacy Policy</a></li>
                <li> <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></i> </li>

            </ul>
        </div>
</nav>
<script>function logfunction()
{
  var a = confirm("Do you want to logout?")
   alert(a)
}
</script>
<!---side bar-->
<div class="body">
<div class="sidebar">
    <!--- Added icon from awesom font--->
    <h2><i class="fas  fa-user-shield"></i> Admin</h2>
    <a href="#" onclick="loadContent('Dashboard')"><i class="fas fa-home"></i> Dashboard</a>
    <a href="#" id="add-batch"><i class="fas fa-layer-group"></i> Add Batch</a>
    <a href="#" onclick="loadContent('add-student')"><i class="fas fa-user-plus"></i> Add Student</a>
    <a href="#" onclick="loadContent('view-student')"><i class="fas fa-users"></i> View Student</a>
    <a href="#" onclick="loadContent('mark-attendance')"><i class="fas fa-check-square"></i> Mark Attendance</a>
    <a href="#" id="view-absent"><i class="fas fa-calendar-check"></i>View Attendance</a>
    <a href="#" id="send-message"><i class="fas fa-envelope"></i> Send Messages</a>
    <p style="color:#e74c3c; margin-top: 20px;"><i class="fas fa-user-shield"></i> Welcome <?php echo $_SESSION['user']; ?></p>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></i>  
</div>
    <div class="content" id="content-area" style="margin-left: 220px; padding: 10px;">
<!---Home Page-->
    <div class="welcome"><h1> Welcome To Online Attendance Recorder App</h1>
    <h1> Manage Student Data</h1>
 <!--Home Page Buttons---> 
    <div class="homec">
    <ul>
        <li><a href="#" id="add-batc">Add Batch</a></li><br>
        <li><a href="#" id="add-studen">Add Student</a></li>
        <li><a href="#" id="view-studen">View Student</a></li>
        <li><a href="#" id="mark-attendanc">Mark Attendance Student</a></li>     
    </ul> 
    </div> 
    </div>
    </div>
</div>

<script>

  function loadContent(type)
  {
    let content=document.getElementById("content-area");

    if(type=== "add-student")
    {
      content.innerHTML=`
      <div class="form-container">
      <h2>Add Student </h2>

      <form method="POST"  action="add_student.php">
      <input type="text" name="roll_no" placeholder="Roll No" required><br><br>
                <input type="text" name="name" placeholder="Name" required><br><br>
                <input type="email" name="email" placeholder="Email"><br><br>
                <input type="date" name="dob" required><br><br>
                <button type="submit">Add Student</button>
            </form>
        </div>`;
    }
    else if(type==="mark-attendance")
    {
      content.innerHTML=`<iframe src="mark_attendance.php" width="100%" height="500px" style="border:none;"></iframe>`;
    } 
     else if (type === "mark-attendance") {
        content.innerHTML = `
        <iframe src="mark_attendance.php" width="100%" height="500px" style="border:none;"></iframe>
        `;
    }

    else {
        content.innerHTML = "<h2>Dashboard</h2>";
    }


  }
</script>
</body>
</html>
