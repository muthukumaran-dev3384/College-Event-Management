<?php
session_start();
include "../db.php";

/* ================= AUTH ================= */
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = (int)$_SESSION['student_id'];

/* ================= STUDENT DETAILS ================= */
$stmt = $conn->prepare(
    "SELECT name, roll_no, branch 
     FROM student 
     WHERE id = ?"
);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("Student not found");
}

$student = $res->fetch_assoc();
$branch  = trim($student['branch']);

$_SESSION['student_name'] = $student['name'];
$_SESSION['roll_no']      = $student['roll_no'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Dashboard</title>

<style>
*{box-sizing:border-box}
body{margin:0;font-family:Poppins,sans-serif;background:#f2f5ff}
.wrapper{display:flex;min-height:100vh}
.sidebar{
    width:270px;
    background:linear-gradient(180deg,#1d2671,#2b5876);
    color:#fff;padding:25px
}
.sidebar h2{text-align:center}
.sidebar .info{text-align:center;font-size:14px;margin-bottom:25px}
.menu a{
    display:block;padding:12px;margin-bottom:12px;
    background:rgba(255,255,255,.15);
    color:#fff;text-decoration:none;border-radius:12px
}
.menu a:hover{background:rgba(255,255,255,.3)}
.logout{
    display:block;margin-top:25px;background:#dc3545;
    text-align:center;padding:12px;border-radius:12px;
    color:#fff;text-decoration:none
}
.main{flex:1;padding:30px}
.section{
    display:none;background:#fff;padding:25px;
    border-radius:20px;
    box-shadow:0 18px 40px rgba(0,0,0,.15)
}
.section.active{display:block}
table{width:100%;border-collapse:collapse;margin-top:15px}
th,td{padding:12px;border-bottom:1px solid #ddd;text-align:center}
th{background:#0d6efd;color:#fff}
.empty{text-align:center;color:#777;padding:20px}
.btn{
    padding:8px 14px;border-radius:8px;
    background:#28a745;color:#fff;
    text-decoration:none;font-weight:600
}
.paid{background:#ffc107;color:#000}
.notify{
    display:flex;gap:12px;
    background:#eef3ff;padding:14px;
    border-left:6px solid #0d6efd;
    border-radius:12px;margin-bottom:12px
}
.notify span{font-size:12px;color:#555}
</style>

<script>
function showSection(id){
    document.querySelectorAll('.section').forEach(s=>s.classList.remove('active'));
    document.getElementById(id).classList.add('active');
}
</script>
</head>

<body>
<div class="wrapper">

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>🎓 Student</h2>
    <div class="info">
        <b><?= htmlspecialchars($_SESSION['student_name']) ?></b><br>
        Roll No: <?= htmlspecialchars($_SESSION['roll_no']) ?><br>
        Dept: <?= htmlspecialchars($branch) ?>
    </div>

    <div class="menu">
        <a href="#" onclick="showSection('notify')">🔔 Notifications</a>
        <a href="#" onclick="showSection('free')">🆓 Free Events</a>
        <a href="#" onclick="showSection('paid')">💰 Paid Events</a>
        <a href="#" onclick="showSection('registered')">📌 My Registrations</a>
    </div>

    <a href="logout.php" class="logout">🚪 Logout</a>
</div>

<div class="main">

<!-- ================= NOTIFICATIONS ================= -->
<div class="section active" id="notify">
<h3>🔔 Event Notifications</h3>

<?php
$nStmt = $conn->prepare("
    SELECT n.message, n.created_at, e.event_name
    FROM notifications n
    JOIN events e ON n.event_id = e.id
    WHERE e.department = ?
    ORDER BY n.created_at DESC
");
$nStmt->bind_param("s", $branch);
$nStmt->execute();
$nRes = $nStmt->get_result();

if ($nRes->num_rows > 0) {
    while ($n = $nRes->fetch_assoc()) {
        echo "
        <div class='notify'>
            <div>📢</div>
            <div>
                <b>{$n['event_name']}</b>
                <p>{$n['message']}</p>
                <span>".date("d M Y • h:i A",strtotime($n['created_at']))."</span>
            </div>
        </div>";
    }
} else {
    echo "<p class='empty'>No notifications available</p>";
}
?>
</div>

<!-- ================= FREE EVENTS ================= -->
<div class="section" id="free">
<h3>🆓 Free Events</h3>
<table>
<tr><th>Event</th><th>Date</th><th>Time</th><th>Venue</th><th>Action</th></tr>

<?php
$fStmt = $conn->prepare("
    SELECT id,event_name,event_date,event_time,venue
    FROM events
    WHERE department = ?
      AND fee = 0
      AND event_date >= CURDATE()
");
$fStmt->bind_param("s", $branch);
$fStmt->execute();
$fRes = $fStmt->get_result();

if ($fRes->num_rows > 0) {
    while ($e = $fRes->fetch_assoc()) {
        echo "<tr>
            <td>{$e['event_name']}</td>
            <td>{$e['event_date']}</td>
            <td>{$e['event_time']}</td>
            <td>{$e['venue']}</td>
            <td><a class='btn' href='register_event.php?event_id={$e['id']}'>Register</a></td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='5' class='empty'>No free events</td></tr>";
}
?>
</table>
</div>

<!-- ================= PAID EVENTS ================= -->
<div class="section" id="paid">
<h3>💰 Paid Events</h3>
<table>
<tr><th>Event</th><th>Date</th><th>Time</th><th>Venue</th><th>Fee</th><th>Action</th></tr>

<?php
$pStmt = $conn->prepare("
    SELECT id,event_name,event_date,event_time,venue,fee
    FROM events
    WHERE department = ?
      AND fee > 0
      AND event_date >= CURDATE()
");
$pStmt->bind_param("s", $branch);
$pStmt->execute();
$pRes = $pStmt->get_result();

if ($pRes->num_rows > 0) {
    while ($e = $pRes->fetch_assoc()) {
        echo "<tr>
            <td>{$e['event_name']}</td>
            <td>{$e['event_date']}</td>
            <td>{$e['event_time']}</td>
            <td>{$e['venue']}</td>
            <td>₹{$e['fee']}</td>
            <td><a class='btn paid' href='register_event.php?event_id={$e['id']}'>Register</a></td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='6' class='empty'>No paid events</td></tr>";
}
?>
</table>
</div>

<!-- ================= REGISTRATIONS ================= -->
<div class="section" id="registered">
<h3>📌 My Registrations</h3>
<table>
<tr><th>Event</th><th>Games</th><th>Payment Ref</th></tr>

<?php
$rStmt = $conn->prepare("
    SELECT e.event_name, r.games_selected, r.payment_ref
    FROM registrations r
    JOIN events e ON r.event_id = e.id
    WHERE r.student_id = ?
");
$rStmt->bind_param("i", $student_id);
$rStmt->execute();
$rRes = $rStmt->get_result();

if ($rRes->num_rows > 0) {
    while ($r = $rRes->fetch_assoc()) {
        echo "<tr>
            <td>{$r['event_name']}</td>
            <td>{$r['games_selected']}</td>
            <td>{$r['payment_ref']}</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='3' class='empty'>No registrations</td></tr>";
}
?>
</table>
</div>

</div>
</div>
</body>
</html>