
<?php
//STUDENT FORM
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: student.html");
    exit();
}

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>

<?php
$college_code = $_SESSION['college_code'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width= , initial-scale=1.0">
    <title>Document</title>
    <style>
* {
    text-decoration: none;
}

.navbar {

    background:#2c3e50;
    font-family: cursive;
    padding-right: 15px;
    padding-left: 15px;
    padding-bottom: 20px;
   
}

.navdiv {
  
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.logo a {
    font-size: 20px;
    font-weight: 500;
    color: white;
}

li {
    list-style: none;
    display: inline-block;
}

li a {
    color: white;
    font-size: 18px;
    font-weight: bold;
    margin-right: 25px;
}

li a:hover {
    color: red;
    
}
#name
{
    color:black;
}

.sidebar
{
  position: fixed;
   width: 200px;
   height:100vh;
   background-color: #2c3e50;
   padding: 20px;
   color: white;
}
.sidebar a{
    display: block;
    color:white;
    padding: 10px 0;
    text-decoration: none;
    transition: 0.3s;
}

.sidebar a:hover
{
  
    border-radius: 5px;
    padding-left: 10px;
}

.sidebar h2{
    color: orange;
    font-size: 20px;
    margin-bottom: 30px;
}

body, html {
    margin: 0;
    padding: 0;
    height: 100%;
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
.sidebar .Logout
{

background-color:rgb(230, 16, 16);
padding: 10px;
width:80px;
border: none;
color: white;
cursor: pointer;
border-radius: 5px;
}

.sidebar .Logout:hover
{
  background-color:brown:
}

.form-container
{
    max-width: 400px;
    margin: 90px;
    padding: 10px;
    border-radius: 10px;
    box-shadow: 0 0 10px #ccc;
    text-align: left;
}
.form-container h2
{
    text-align: center;
}


th,td
{
    padding:10px;
}
h1
    {
        text-align:center;
        font-size:30px;
        color:black;
        font-family: monospace;
        text-shadow:3px 3px 5px #435353;

    }
</style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <nav class="navbar">
    <div class="navdiv">
      <div class="logo"><a href="#">Online Attendance Recorder</a></div>
      <ul>
        <li><a href="log.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
      </ul>
    </div>
  </nav>

  <div class="sidebar">
    <h2><i class="fas fa-user"></i> Student</h2>
  <a href="#" onclick="loadContent('view-student')"><i class="fas fa-users"></i> View Student</a>
    <p style="color:#e74c3c"><i class="fas fa-user"></i> Welcome <?php echo $_SESSION['user']; ?></p>
    <button class="Logout">
     <a href="log.php"><i class="fas fa-sign-out-alt"></i> Logout</a></i>
    </button>
  </div>

  <div id="content-area" style="margin-left:200px; padding:20px;">
    <div class="welcome">
      <h1>Welcome To Student Panel</h1>
    </div>
  </div>

  <script>
   function loadContent(type)
  {
    let content=document.getElementById("content-area");

    if( type==="view-student")
     {
        content.innerHTML=`<iframe src="studentpanel.php" width="100%" height="800px" style="border:none;"></iframe> `;

     }
 
    else {
        content.innerHTML = "<h2>Dashboard</h2>";
    }


  }
  </script>
</body>
</html>
