<!--files:interface.php, inter.php-->
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
                <li><a href="logout.php" onclick="logfunction()"><i class="fas fa-sign-out-alt"></i>Logout</a></li>

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
    <a href="#" id="Dashboard"><i class="fas fa-home"></i> Dashboard</a>
    <a href="#" id="add-batch"><i class="fas fa-layer-group"></i> Add Batch</a>
    <a href="#" id="add-student"><i class="fas fa-user-plus"></i> Add Student</a>
    <a href="#" id="view-student"><i class="fas fa-users"></i> View  Student</a>
    <a href="#" id="mark-attendance"><i class="fas fa-check-square"></i> Mark Attendance</a>
    <a href="#" id="view-absent"><i class="fas fa-calendar-check"></i>View Attendance</a>
    <a href="#" id="send-message"><i class="fas fa-envelope"></i> Send Messages</a>
    <p style="color:#e74c3c; margin-top: 20px;"><i class="fas fa-user-shield"></i> Welcome Admin</p>
    <button class="Logout"><a href="logout.php" onclick="logfunction()"><i class="fas fa-sign-out-alt"></i> Logout</a></button>
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
    //For Home Page Buttons
  // 1) Map link IDs → PHP endpoints
  const linkMap = {
    'add-batc':       'add_batch_view.php',
    'add-studen':     'add_student.php',
    'view-studen':    'view_student_form.php',
    'delete-studen':  'deletestudentsidebar.php',
    'update-studen':  'updatestudentsidebar.php',
    'mark-attendanc': 'mark_attendance.php',
    'view-absen':     'view_absent_form.php',
  };
  // 2) Generic loader: fetch → inject → bind page-specific JS
  function loadSection(id) {
    const url = linkMap[id]; 
    if (!url) return;
    fetch(url)
      .then(r => r.text())
      .then(html => {
        const cont = document.getElementById('content-area');
        cont.innerHTML = html;
        // always bind batch→class if present
        bindBatchClass(cont);
        // then page-specific binders:
        if (id === 'view-studen')    bindViewStudent(cont);
        if (id === 'mark-attendanc') bindMarkAttendance(cont);
        if (id === 'send-messag')    bindSendMessages(cont);
      })
      .catch(err => console.error('Error loading', id, err));
  }
  // 3) On DOM ready, hook up all sidebar & homec links
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.sidebar a[id], .homec a[id]').forEach(a => {
      if (linkMap[a.id]) {
        a.addEventListener('click', e => {
          e.preventDefault();
          loadSection(a.id);
        });
      }
    });
 
  });
  // 4) Binder for batch→class (works for any page with #batchSelect/#batch and #classSelect/#class)
  function bindBatchClass(container) {
    const bs = container.querySelector('#batchSelect, #batch');
    const cs = container.querySelector('#classSelect, #class');
    if (!bs || !cs) return;
    cs.disabled = true;
    bs.addEventListener('change', () => {
      cs.innerHTML = '<option value="">-- Select Class --</option>';
      cs.disabled = true;
      const batch = bs.value;
      if (!batch) return;
      fetch('get_classes.php?batch=' + encodeURIComponent(batch))
        .then(r => r.json())
        .then(list => {
          list.forEach(c => {
            const o = document.createElement('option');
            o.value = o.textContent = c;
            cs.appendChild(o);
          });
          cs.disabled = false;
        })
        .catch(console.error);
    });
  }
  // 5a) Binder for View Student page
  function bindViewStudent(c) {
    const form = c.querySelector('#view-student-form');
    if (!form) return;
    form.addEventListener('submit', e => {
      e.preventDefault();
      const batch = c.querySelector('#batchSelect').value;
      const cls   = c.querySelector('#classSelect').value;
      fetch(`view_student.php?batch=${encodeURIComponent(batch)}&class=${encodeURIComponent(cls)}`)
        .then(r => r.text())
        .then(html => {
          c.querySelector('#student-list').innerHTML = html;
        })
        .catch(console.error);
    });
  }
  // 5b) Binder for Mark Attendance page
  function bindMarkAttendance(c) {
    const loadBtn   = c.querySelector('#load-students');
    const dateInput = c.querySelector('#dateInput');
    const listDiv   = c.querySelector('#attendanceList');
    if (!loadBtn || !dateInput || !listDiv) return;

    loadBtn.addEventListener('click', () => {
      const batch = c.querySelector('#batchSelect').value;
      const cls   = c.querySelector('#classSelect').value;
      const date  = dateInput.value;
      if (!batch||!cls||!date) return alert('Select all fields');
      fetch(`mark_attendance.php?batch=${batch}&class=${cls}&date=${date}`)
        .then(r => r.text())
        .then(html => {
          listDiv.innerHTML = html;
          // bind save buttons if any
          listDiv.querySelectorAll('.save-btn').forEach(b => {
            b.addEventListener('click', () => {
              const id = b.dataset.id;
              const status = listDiv.querySelector(`.status-dropdown[data-id="${id}"]`).value;
              fetch('update_attendance.php', {
                method: 'POST',
                headers:{'Content-Type':'application/x-www-form-urlencoded'},
                body:`id=${id}&status=${status}`
              }).then(r=>r.text()).then(msg=>alert(msg));
            });
          });
        })
        .catch(console.error);
    });
  }

    document.getElementById("Dashboard").addEventListener("click", function (e) {
    e.preventDefault();
    fetch('dashboard.php')
        .then(response => response.text())
        .then(html => {
            document.getElementById("content-area").innerHTML = html;
        })
        .catch(error => console.error("Error loading home dashboard:", error));
});
window.onload = function () {
    fetch('dashboard.php')
        .then(response => response.text())
        .then(html => {
            document.getElementById("content-area").innerHTML = html;
        });

    // Handle student_success redirect
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('student_success') === 'true') {
        document.getElementById("add-student").click();
        const newUrl = window.location.origin + window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
    }
};

function initializeDashboardFeatures() {
  // Initialize trendChart
  const trendCanvas = document.getElementById('trendChart');
  if (trendCanvas) {
    const labels = JSON.parse(trendCanvas.dataset.labels);
    const data = JSON.parse(trendCanvas.dataset.data);
    new Chart(trendCanvas.getContext('2d'), {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Total Absences',
          data: data,
          backgroundColor: 'rgba(255,99,132,0.2)',
          borderColor: 'rgba(255,99,132,1)',
          borderWidth: 2,
          fill: true,
          tension: 0.4
        }]
      }
    });
  }

  // Export CSV button
  const exportBtn = document.getElementById('exportLowAttendanceBtn');
  if (exportBtn) {
    exportBtn.addEventListener('click', () => {
      const rows = [...document.querySelectorAll("#low-table tr")];
      let csv = rows.map(row =>
        [...row.children].map(cell => `"${cell.innerText.trim()}"`).join(",")
      ).join("\n");

      const blob = new Blob([csv], { type: 'text/csv' });
      const a = document.createElement("a");
      a.href = URL.createObjectURL(blob);
      a.download = "low_attendance.csv";
      a.click();
    });
  }
}

//// JS for handling the dashboard link
document.getElementById('Dashboard').addEventListener('click', function(e) {
  e.preventDefault();
  fetch('dashboard.php')
    .then(r => r.text())
    .then(html => {
      document.getElementById('content-area').innerHTML = html;
      initializeDashboardFeatures();
    });
});



</script>

<script>
//Step 1: Add Batch Form 
document.addEventListener("click", function (e) {
    if (e.target && e.target.id === "add-batch") {
        e.preventDefault();
        fetch("add_batch_view.php")
            .then(res => res.text())
            .then(html => {
                document.getElementById("content-area").innerHTML = html;
                // After loading the content, check for batch success
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('success') === 'true') {
                    const successMessage = document.createElement("div");
                    successMessage.className = "success-message";
                    successMessage.textContent = "Batch inserted successfully!";
                    document.getElementById("content-area").prepend(successMessage);
                    // Clean the URL
                    const newUrl = window.location.origin + window.location.pathname;
                    window.history.replaceState({}, document.title, newUrl);
                }
            }); } });
</script>
<script>
//Step 1: Add Batch Form 
document.getElementById("add-batch").addEventListener("click", function (e) {
e.preventDefault();    
// Check for success message in the URL
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('success') === 'true') {
    const successMessage = document.createElement("p");
    successMessage.textContent = "Batch inserted successfully!";
    successMessage.style.color = "green";
    successMessage.style.fontWeight = "bold";
    document.getElementById("content-area").prepend(sucessMessage);
}
fetchBatches();
});
// Function to add new class input
function addClassField() {
    const classDiv = document.getElementById("class-fields"); //get addclassfield and store in div
    const input = document.createElement("input");
    input.type = "text";
    input.name = "classes[]";
    input.placeholder = "Enter Another class";
    classDiv.appendChild(input);//create input field and store in div
    classDiv.appendChild(document.createElement("br"));
        classDiv.appendChild(document.createElement("br"));
}
// Function to fetch and display batches
function fetchBatches() {
fetch('add_batch.php?fetch_batches=true')
.then(response => response.json())
.then(data => {
    const batchList = document.getElementById("batch-list");
    batchList.innerHTML = ""; // Clear existing content
    const orderedList = document.createElement("ol");
    for (const [batch, classes] of Object.entries(data)) {
        const batchItem = document.createElement("li");
        batchItem.innerHTML = `<strong>${batch}</strong>`;

        const classList = document.createElement("ul");
        classes.forEach(cls => {
            const classItem = document.createElement("li");
            classItem.textContent = cls;
            classList.appendChild(classItem);
        });
        batchItem.appendChild(classList);
        orderedList.appendChild(batchItem);
    }
    batchList.appendChild(orderedList);
})
.catch(error => console.error("Error fetching batches:", error));
}
// Automatically load the "Add Batch" section if redirected with success
window.onload = function () {
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('success') === 'true') {
document.getElementById("add-batch").click();

// Remove ?success=true from the URL so it doesn't trigger again
const newUrl = window.location.origin + window.location.pathname;
window.history.replaceState({}, document.title, newUrl);
}
};
</script>
<!--Step 2: View Added Batch--->
<script>
    document.getElementById("add-batch").addEventListener("click", function (e) {
        e.preventDefault();
        fetch("add_batch_view.php")
            .then(response => response.text())
            .then(html => {
                document.getElementById("content-area").innerHTML = html;     
const batchSelect = document.querySelector("#content-area #batch");
const classSelect = document.querySelector("#content-area #class");

if (batchSelect && classSelect) {
batchSelect.addEventListener("change", function () {
    const batch = this.value;
    classSelect.innerHTML = '<option value="">--Select Class--</option>';

    if (!batch) return;
    fetch('get_classes.php?batch=' + encodeURIComponent(batch))
        .then(response => response.json())
        .then(data => {
            if (Array.isArray(data)) {
                data.forEach(className => {
                    const option = document.createElement('option');
                    option.value = className;
                    option.textContent = className;
                    classSelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error fetching classes:', error);
        });
}); }  })
    .catch(error => {
        console.error("Error loading Add Batch content:", error);
    });
});
</script> <script>
        // Function to bind the batch-to-class dropdown behavior
        function bindBatchToClassDropdown() {
            const batchSelect = document.querySelector("#content-area #batch");
            const classSelect = document.querySelector("#content-area #class");
            if (batchSelect && classSelect) {
                batchSelect.addEventListener("change", function () {
                    const batch = this.value;
                    classSelect.innerHTML = '<option value="">--Select Class--</option>';
                    if (!batch) return;
                    fetch('get_classes.php?batch=' + encodeURIComponent(batch))
                        .then(response => response.json())
                        .then(data => {
                            if (Array.isArray(data)) {
                                data.forEach(className => {
                                    const option = document.createElement('option');
                                    option.value = className;
                                    option.textContent = className;
                                    classSelect.appendChild(option);
                                }); } })
                        .catch(error => {
                            console.error('Error fetching classes:', error);
                        });  }); } }  
        // Click handler for "Add Student"
        document.getElementById("add-student").addEventListener("click", function (e) {
            e.preventDefault(); // Prevent default link behavior if it's an <a>
            fetch("add_student.php")
                .then(response => response.text())
                .then(html => {
                    // Load the HTML into the page
                    document.getElementById("content-area").innerHTML = html;
        
                    // Bind dropdown behavior
                    bindBatchToClassDropdown();
                })
                .catch(error => {
                    console.error("Error loading Add Student content:", error);
                });
        });   
        </script>
        <script>
            document.getElementById("view-student").addEventListener("click", function (e) {
                e.preventDefault();
                fetch("view_student_form.php")
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById("content-area").innerHTML = html;
                    })
                    .catch(error => {
                        console.error("Error loading View Student form:", error);
                    });
            });
            </script> <script>
            document.getElementById("view-student").addEventListener("click", function (e) {
                e.preventDefault();
                fetch("view_student_form.php")
    .then(response => response.text())
    .then(html => {
        document.getElementById("content-area").innerHTML = html;
        const batchSelect = document.querySelector("#batchSelect");
        const classSelect = document.querySelector("#classSelect");
        if (batchSelect && classSelect) {
            batchSelect.addEventListener("change", function () {
                const batch = this.value;
                classSelect.innerHTML = '<option value="">-- Select Class --</option>';
                if (!batch) return;
                fetch('get_classes.php?batch=' + encodeURIComponent(batch))
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(className => {
                            const option = document.createElement('option');
                            option.value = className;
                            option.textContent = className;
                            classSelect.appendChild(option);
                        });
                    });
            });
        }
        // ✅ ADD THIS: re-bind form submit
        const viewForm = document.querySelector("#view-student-form");
        if (viewForm) {
            viewForm.addEventListener("submit", function (e) {
                e.preventDefault();
                const batch = document.getElementById("batchSelect").value;
                const className = document.getElementById("classSelect").value;
                fetch('view_student.php?batch=' + encodeURIComponent(batch) + '&class=' + encodeURIComponent(className))
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById("student-list").innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error loading students:', error);
                    }); }); }  }) })
        </script>          
<script>
// 1) Load and bind the “View Absent” form
document.getElementById("view-absent").addEventListener("click", function(e) {
  e.preventDefault();
  fetch("view_absent_form.php")
    .then(r => r.text())
    .then(html => {
      document.getElementById("content-area").innerHTML = html;
      bindAbsentStudentForm();
    })
    .catch(console.error);
});
function bindAbsentStudentForm() {
  const batchSelect = document.querySelector("#batchSelect");
  const classSelect = document.querySelector("#classSelect");
  const form        = document.querySelector("#view-absent-form");
  // 2) Batch → Class cascade
  if (batchSelect && classSelect) {
    batchSelect.addEventListener("change", function() {
      classSelect.innerHTML = '<option value="">-- Select Class --</option>';
      if (!this.value) return;
      fetch(`get_classes.php?batch=${encodeURIComponent(this.value)}`)
        .then(r => r.json())
        .then(data => {
          data.forEach(cn => {
            let o = document.createElement("option");
            o.value = o.textContent = cn;
            classSelect.appendChild(o);
          });
        })
        .catch(console.error);
    });
  }
  // 3) On form submit, load the attendance table
  if (form) {
    form.addEventListener("submit", function(e) {
      e.preventDefault();
      const b = encodeURIComponent(this.batch.value);
      const c = encodeURIComponent(this.class.value);
      const d = encodeURIComponent(this.date.value);

      fetch(`view_attendance.php?batch=${b}&class=${c}&date=${d}`)
        .then(r => r.text())
        .then(html => {
          document.getElementById("student-alist").innerHTML = html;
          bindSaveButtonEvents();
          bindDeleteButtonEvents();
        })
        .catch(console.error);
    });
  }
}
// 4) Save button binding
function bindSaveButtonEvents() {
  document.querySelectorAll(".save-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      const tr     = btn.closest("tr");
      const id     = tr.dataset.id;
      const status = tr.querySelector(".status-dropdown").value;
      const dateEl = document.getElementById("dateInput");   // your date picker
      const date   = dateEl ? dateEl.value : "";
      if (!date) {
        alert("Please select a date before saving.");
        return;
      }
      fetch("update_attendance.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${encodeURIComponent(id)}&status=${encodeURIComponent(status)}&date=${encodeURIComponent(date)}`
      })
      .then(r => r.text())
      .then(msg => alert(msg))
      .catch(console.error);
    });
  });
}
// 5) Delete button binding
function bindDeleteButtonEvents() {
  document.querySelectorAll(".delete-btn").forEach(btn => {
    btn.addEventListener("click", function() {
      const tr = this.closest("tr");
      const id = tr.dataset.id;
      if (!confirm("Are you sure you want to delete this attendance record?")) return;

      fetch("delete_attendance.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${encodeURIComponent(id)}`
      })
      .then(r => r.text())
      .then(msg => {
        alert(msg);
        tr.remove();  // remove the row immediately
      })
      .catch(console.error);
    });
  });
}
</script>  
</script>
         <script>
            // Handle "Mark Attendance" click
            document.getElementById('mark-attendance').addEventListener('click', function(e) {
                e.preventDefault();
                fetch('mark_attendance.php')
                    .then(r => r.text())
                    .then(html => {
                        document.getElementById('content-area').innerHTML = html;
                        initAttendanceUI();
                        bindLoadStudents();
                        bindSubmitAttendance();
                    })
                    .catch(console.error);
            });  
            // Batch → Class dropdown logic
            function initAttendanceUI() {
                const batch = document.getElementById('batchSelect');
                const cls = document.getElementById('classSelect');
                if (!batch || !cls) return;
            
                batch.addEventListener('change', () => {
                    cls.innerHTML = '<option value="">-- Select Class --</option>';
                    if (!batch.value) return;
            
                    fetch('get_classes.php?batch=' + encodeURIComponent(batch.value))
                        .then(r => r.json())
                        .then(list => {
                            list.forEach(c => {
                                const o = document.createElement('option');
                                o.value = o.textContent = c;
                                cls.appendChild(o);
                            });
                        })
                        .catch(console.error);
                });
            }  
            // Bind “Load Students” button
            function bindLoadStudents() {
                const btn = document.getElementById('load-students');
                if (!btn) return;
            
                btn.addEventListener('click', () => {
                    const batch = document.getElementById('batchSelect').value;
                    const cls = document.getElementById('classSelect').value;
                    const date = document.getElementById('dateInput').value;
                    if (!batch || !cls || !date) {
                        return alert('Please select batch, class and date.');
                    }
                    const qs = new URLSearchParams({ batch, class: cls, date }).toString();
                    fetch('mark_attendance.php?' + qs)
                        .then(r => r.text())
                        .then(html => {
                            document.getElementById('content-area').innerHTML = html;
                            initAttendanceUI();
                            bindLoadStudents();
                            bindSubmitAttendance();
                        })
                        .catch(console.error);
                });
            } 
            // Bind “Submit Attendance” button
            function bindSubmitAttendance() {
                const submitBtn = document.querySelector('.submit-btn');
                if (!submitBtn) return;
            
                submitBtn.addEventListener('click', () => {
                    const form = document.getElementById('submit-form');
                    const data = new FormData(form);
            
                    fetch('mark_attendance.php', { method: 'POST', body: data })
                        .then(r => r.text())
                        .then(html => {
                            document.getElementById('content-area').innerHTML = html;
            
                            const box = document.querySelector('.form-box');
                            if (box && !box.querySelector('.message')) {
                                const msg = document.createElement('div');
                                msg.className = 'message';
                                msg.textContent = 'Attendance submitted successfully!';
                                box.prepend(msg);
                            }
            
                            initAttendanceUI();
                            bindLoadStudents();
                            bindSubmitAttendance();
                        })
                        .catch(console.error);
                });
            }
            </script>
<<script>
document.getElementById("send-message").addEventListener("click", function(e) {
  e.preventDefault();
  fetch("send_message_form.php")
    .then(res => res.text())
    .then(html => {
      // 1) Inject the form
      document.getElementById("content-area").innerHTML = html;
      // 2) Grab freshly injected elements
      const batchSelect  = document.getElementById("batchSelect");
      const classSelect  = document.getElementById("classSelect");
      const dateInput    = document.getElementById("attendanceDate");
      const loadBtn      = document.getElementById("loadBtn");
      const absentList   = document.getElementById("absentList");
      // 3) Re-bind Batch→Class (you already have this)
      batchSelect.addEventListener("change", () => {
        classSelect.innerHTML = '<option value="">-- Select Class --</option>';
        classSelect.disabled = true;
        const batch = batchSelect.value;
        if (!batch) return;
        fetch("get_classes.php?batch=" + encodeURIComponent(batch))
          .then(r => r.json())
          .then(list => {
            list.forEach(c => {
              const o = document.createElement("option");
              o.value = o.textContent = c;
              classSelect.appendChild(o);
            });
            classSelect.disabled = false;
          });
      });
      // 4) Bind the Load Absent Students button
      loadBtn.addEventListener("click", () => {
        const batch = batchSelect.value;
        const cls   = classSelect.value;
        const date  = dateInput.value;

        if (!batch || !cls || !date) {
          alert("Please select batch, class and date.");
          return;
        }
        absentList.innerHTML = "Loading…";
        fetch(`get_absent_students.php?batch=${encodeURIComponent(batch)}&class=${encodeURIComponent(cls)}&date=${encodeURIComponent(date)}`)
          .then(r => r.json())
          .then(students => {
            // clear
            absentList.innerHTML = "";

            if (students.message) {
              absentList.innerHTML = `<p>${students.message}</p>`;
              return;
            }
            const table = document.createElement("table");
            table.innerHTML = `
 <caption class="cap">Absent Students</caption>
              <thead>
                <tr>
                  <th>Name</th><th>Class</th><th>Batch</th>
                  <th>Status</th><th>Send Message</th>
                </tr>
              </thead>
              <tbody></tbody>
            `;
            const tbody = table.querySelector("tbody");
            students.forEach(s => {
              const tr = document.createElement("tr");
              tr.innerHTML = `
                <td>${s.name}</td>
                <td>${s.class_name}</td>
                <td>${s.batch_name}</td>
                <td>Absent</td>
                <td>
                  <button class="sendMessageBtn"
                          data-id="${s.id}"
                          data-phone="${s.phone}">
                    Send Message
                  </button>
                </td>
              `;
              tbody.appendChild(tr);
            });
            absentList.appendChild(table);

            // 5) Attach the Send‐Message handlers
            table.querySelectorAll(".sendMessageBtn").forEach(btn => {
              btn.addEventListener("click", () => {
                fetch("send_sms.php", {
                  method: "POST",
                  headers: { "Content-Type": "application/x-www-form-urlencoded" },
                  body: `id=${encodeURIComponent(btn.dataset.id)}&phone=${encodeURIComponent(btn.dataset.phone)}`
                })
                .then(r => r.text())
                .then(msg => alert(msg))
                .catch(err => {
                  console.error("SMS error:", err);
                  alert("Failed to send SMS.");
                });
              });
            });
          })
          .catch(err => {
            console.error("Fetch absent error:", err);
            absentList.innerHTML = "<p>Error loading absent students.</p>";
          });
      });

    })
    .catch(err => {
      console.error("Load form error:", err);
      alert("Could not load form.");
    });
});
</script>  
</body>
</html>
