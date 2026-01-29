<?php
session_start();
include "../db.php";

/* ================= AUTH CHECK ================= */
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = (int)$_SESSION['student_id'];

/* ================= FETCH STUDENT ================= */
$stmt = $conn->prepare(
    "SELECT name, roll_no, branch 
     FROM student 
     WHERE id = ?"
);

if (!$stmt) {
    die("Student SQL Error: " . $conn->error);
}

$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    die("Student not found");
}

$student_name = $student['name'];
$student_roll = $student['roll_no'];
$student_dept = trim($student['branch']); // IMPORTANT
?>

<!DOCTYPE html>
<html>
<head>
<title>Free Events</title>

<style>
*{box-sizing:border-box}
body{
    margin:0;
    font-family:Poppins,Segoe UI,sans-serif;
    background:linear-gradient(135deg,#141e30,#243b55);
}
.wrapper{display:flex;min-height:100vh}

/* ===== SIDEBAR ===== */
.sidebar{
    width:260px;
    background:#0b1c2d;
    color:#fff;
    padding:25px;
}
.sidebar h2{text-align:center}
.info{
    text-align:center;
    font-size:14px;
    margin-bottom:25px;
}
.menu a{
    display:block;
    padding:14px;
    margin-bottom:12px;
    background:#1c2f45;
    color:#fff;
    text-decoration:none;
    border-radius:10px;
    transition:.3s;
}
.menu a:hover{
    background:#355c7d;
    transform:translateX(6px);
}
.logout{
    display:block;
    margin-top:20px;
    text-align:center;
    background:#dc3545;
    padding:12px;
    border-radius:10px;
    color:#fff;
    text-decoration:none;
}

/* ===== MAIN ===== */
.main{
    flex:1;
    padding:40px;
    background:#f4f7fb;
}
.card{
    background:#fff;
    padding:30px;
    border-radius:20px;
    box-shadow:0 25px 45px rgba(0,0,0,.18);
}
h3{
    margin-top:0;
    color:#243b55;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}
th{
    background:#0d6efd;
    color:#fff;
    padding:14px;
}
td{
    padding:14px;
    border-bottom:1px solid #ddd;
    text-align:center;
}
tr:nth-child(even){
    background:#f1f5ff;
}

.status-open{
    color:green;
    font-weight:700;
}
.status-closed{
    color:red;
    font-weight:700;
}

.btn{
    padding:8px 16px;
    background:#198754;
    color:#fff;
    border-radius:8px;
    text-decoration:none;
    font-weight:600;
}
.btn.rules{
    background:#6f42c1;
}
.btn.disabled{
    background:#6c757d;
    cursor:not-allowed;
}

.empty{
    text-align:center;
    padding:25px;
    color:#777;
}
.back{
    display:inline-block;
    margin-top:20px;
    padding:10px 18px;
    background:#6c757d;
    color:#fff;
    border-radius:10px;
    text-decoration:none;
}
</style>
</head>

<body>

<div class="wrapper">




<!-- ===== MAIN ===== -->
<div class="main">
<div class="card">

<h3>🆓 Free Department Events</h3>

<table>
<tr>
    <th>Event</th>
    <th>Date</th>
    <th>Time</th>
    <th>Venue</th>
    <th>Rules</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php
$sql = "
    SELECT id, event_name, event_date, event_time, venue, rules_file, status
    FROM events
    WHERE department = ?
      AND fee = 0
    ORDER BY event_date
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Free Event SQL Error: " . $conn->error);
}

$stmt->bind_param("s", $student_dept);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {

    while ($e = $res->fetch_assoc()) {

        /* ===== STATUS & ACTION ===== */
        if ($e['status'] === 'open') {
            $status = "<span class='status-open'>OPEN</span>";
            $action = "<a class='btn' href='register_event.php?event_id={$e['id']}'>Register</a>";
        } else {
            $status = "<span class='status-closed'>CLOSED</span>";
            $action = "<span class='btn disabled'>Closed</span>";
        }

        /* ===== RULES FILE ===== */
        if (!empty($e['rules_file']) && file_exists("../uploads/".$e['rules_file'])) {
            $rules = "<a class='btn rules' href='../uploads/{$e['rules_file']}' download>Download</a>";
        } else {
            $rules = "<span class='btn disabled'>N/A</span>";
        }

        echo "<tr>
            <td>{$e['event_name']}</td>
            <td>{$e['event_date']}</td>
            <td>{$e['event_time']}</td>
            <td>{$e['venue']}</td>
            <td>$rules</td>
            <td>$status</td>
            <td>$action</td>
        </tr>";
    }

} else {
    echo "<tr>
            <td colspan='7' class='empty'>No free events available for your department</td>
          </tr>";
}
?>

</table>

<a href="dashboard.php" class="back">⬅ Back to Dashboard</a>

</div>
</div>

</div>
</body>
</html>