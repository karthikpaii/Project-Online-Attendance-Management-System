<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== "admin") {
    header("Location: home.html");
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
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Online Attendace Recorder</title>
<link href="styles.css" rel="stylesheet" ></link >
<style>
     .btn {
    margin: auto;
    padding: 15px 40px;
    width: 180px;
    height: 70px;
    border: 2px solid white;
    border-radius: 12px ;
    outline: none;
    font-size: 20px;
    padding: 15px;
    color: #fff;
    font-family: 'Times New Roman', Times, serif;
    cursor: pointer;
    position: relative;
    z-index:0;
    border-color: white;
    }
</style>
</head>
<body>
<!--Navigation Bar-->
<nav class="navbar">
<div class="navdiv">
<div class="logo"><a href="#">Online Attendace Recorder</a></div>
<ul>
<li><a href="#">Home</a></li>
<li><a href="about.html">About</a></li>
<li><a href="privacy.html">Privacy Policy</a></li>
</ul>
</div>
</nav>
  
<!--Middle Section-->
<section class="mid">
<div class="tex">
<h1>Online Attendace Recorder</h1>
<p> Manage Attendace Records Easily and Efficiently</p>
<a href="interface.php"> <button class="btn">Go To Admin Panel</button></a>

</div>

</section>

<!---Feature Section -->
<section class="features">
    <h2 class="feature-title"> Features</h2>
        <div class="feature-container">
            <!--First Box-->
            <div class="feature-box">
                <h3>📌Easy Attendance</h3>
                <p>Mark Attendance with Single Click</p>
            </div>

             <!--Second Box-->
             <div class="feature-box">
                <h3>🔒 Secure Data</h3>
                <p>Secure Your Data</p>
            </div>

             <!--Third Box-->
             <div class="feature-box">
                <h3>👤 User Friendly Interface</h3>
                <p>Easy To Understand Interface</p>
            </div>

            <!--Fourth Box-->
            <div class="feature-box">
                <h3>📊 Attendace Report</h3>
                <p>Track Students Attendance</p>
            </div>

            <!--Fifth Box-->
            <div class="feature-box">
                <h3>📩 Send Message</h3>
                <p>Send Messages To Absent Students Parents</p>
            </div>
        </div>
</section>


<!--How it works-->

<section class="howitworks">
   <h2 class="howittitle">How It Works</h2>
    <div class="how-container">
      <div class="box">
        <h3>SignUp/Login</h3>
        <p>Create an account or Login</p>
      </div>

      <div class="box">
        <h3>Create Batch or Course</h3>
        <p>Create New Batch and Class</p>
      </div>

      <div class="box">
        <h3>Mark Attendace</h3>
        <p>Select names and Mark Attendace</p>
      </div>

      <div class="box">
        <h3>View Attendace</h3>
        <p>View All Attendance/ Edit Records</p>
      </div>

      <div class="box">
      <h3>Send Messages</h3>
        <p>Send Message to Absentees Parents</p>
      </div>
   </div>
   <script>
    document.querySelectorAll('.box').forEach((box,index,boxes) => {
        box.addEventListener('mouseover' , () => {
            boxes.forEach((b,i) => {
                let className= i < index ? 'prev' : i === index ? 'hovered' : 'next';
                b.classList.remove("prev","next","hovered");
                b.classList.add(className);
            })
        })
        box.addEventListener('mouseleave',()=>{
            boxes.forEach((b)=>b.classList.remove ("prev","next","hovered"));
        })
    })


    function logfunction()
{
  var a = confirm("Do you want to logout?")
   
}
    </script>
</section>

<!--Footer-->
<footer>
    <p>📧 Contact:attendance@support.com</p>
    <p>© 2025 Online Attendance Recorder. All rights reserved.</p>
</footer>
<!--attendance@support.com - its a fake email-->
</body>
</html> 