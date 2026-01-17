<?php
include "../db.php";


if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

/* FETCH STUDENT DETAILS */
$studentData = $conn->query("
    SELECT name, roll_no, branch 
    FROM student 
    WHERE id = '$student_id'
")->fetch_assoc();

$student_branch = $studentData['branch'];
$_SESSION['student_name'] = $studentData['name'];
$_SESSION['roll_no'] = $studentData['roll_no'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Dashboard</title>

<style>
*{box-sizing:border-box}
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:#f4f6fb;
}
.wrapper{display:flex;min-height:100vh;}
.sidebar{
    width:260px;
    background:linear-gradient(180deg,#1d2671,#2b5876);
    color:#fff;
    padding:25px;
}
.sidebar h2{text-align:center;margin-top:0;}
.sidebar .info{text-align:center;font-size:14px;margin-bottom:20px;}
.menu a{
    display:block;
    padding:12px;
    margin-bottom:10px;
    background:rgba(255,255,255,.1);
    color:#fff;
    text-decoration:none;
    border-radius:8px;
}
.menu a:hover{background:rgba(255,255,255,.25);}
.logout{
    display:block;
    margin-top:20px;
    background:#dc3545;
    text-align:center;
    padding:10px;
    border-radius:8px;
    color:#fff;
    text-decoration:none;
}
.main{flex:1;padding:30px;}
.section{
    display:none;
    background:#fff;
    padding:25px;
    border-radius:15px;
    box-shadow:0 15px 35px rgba(0,0,0,.15);
}
.section.active{display:block;}
.notify{
    background:#eef3ff;
    padding:12px;
    border-left:5px solid #007bff;
    border-radius:8px;
    margin-bottom:10px;
}
table{width:100%;border-collapse:collapse;}
th,td{padding:12px;border-bottom:1px solid #ddd;text-align:center;}
th{background:#007bff;color:#fff;}
.empty{text-align:center;color:#777;padding:15px;}
.btn{
    padding:8px 14px;
    background:#28a745;
    color:#fff;
    border-radius:6px;
    text-decoration:none;
}
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
        <?= $_SESSION['student_name']; ?><br>
        Roll No: <?= $_SESSION['roll_no']; ?>
    </div>

    <div class="menu">
        <a href="#" onclick="showSection('notifications')">🔔 Notifications</a>
        <a href="#" onclick="showSection('events')">📅 Upcoming Events</a>
        <a href="#" onclick="showSection('registered')">📌 My Registrations</a>
    </div>

    <a href="logout.php" class="logout">🚪 Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<!-- NOTIFICATIONS -->
<div class="section active" id="notifications">
<h3>🔔 Notifications</h3>

<?php
$today = date("Y-m-d");

/* SQL QUERY */
$sql = "
    SELECT 
        notifications.message,
        events.event_name,
        events.event_scope,
        events.department
    FROM notifications
    INNER JOIN events ON notifications.event_id = events.id
    WHERE notifications.notify_date <= '$today'
    AND (
        events.event_scope = 'college'
        OR (
            events.event_scope = 'department'
            AND events.department = '$student_branch'
        )
    )
    ORDER BY notifications.notify_date DESC
";

/* RUN QUERY */
$nq = $conn->query($sql);

/* SQL ERROR CHECK */
if (!$nq) {
    die('<b>Database Error:</b> ' . $conn->error);
}

/* DISPLAY NOTIFICATIONS */
if ($nq->num_rows > 0) {
    while ($n = $nq->fetch_assoc()) {

        /* Scope label */
        if ($n['event_scope'] === 'college') {
            $scopeText = "🏫 <b>College Notification</b><br>";
        } else {
            $scopeText = "🎓 <b>{$n['department']} Department</b><br>";
        }

        /* Event name */
        $eventText = $n['event_name']
            ? "📌 Event: <b>{$n['event_name']}</b><br>"
            : "";

        echo "
        <div class='notify'>
            $scopeText
            $eventText
            {$n['message']}
        </div>";
    }
} else {
    echo "<div class='empty'>No notifications available</div>";
}
?>



</div>

<!-- EVENTS -->
<div class="section" id="events">
<h3>📅 Upcoming Events</h3>

<table>
<tr>
    <th>Event</th>
    <th>Date</th>
    <th>Time</th>
    <th>Venue</th>
    <th>Fee</th>
    <th>Status</th>
    <th>Rules</th>
    <th>Action</th>
</tr>

<?php
$events = $conn->query("
    SELECT * FROM events
    WHERE event_date >= CURDATE()
    AND (
        event_scope='college'
        OR (event_scope='department' AND department='$student_branch')
    )
    ORDER BY event_date ASC
");

if ($events && $events->num_rows > 0) {

    while ($e = $events->fetch_assoc()) {

        /* ---------- FEE ---------- */
        $fee = ($e['fee'] == 0) ? "FREE" : "₹ {$e['fee']}";

        /* ---------- RULES ---------- */
        $rules = $e['rules_file']
            ? "<a class='btn' href='../uploads/{$e['rules_file']}' download>Download</a>"
            : "N/A";

        /* ---------- STATUS ---------- */
        if ($e['status'] === 'open') {
            $statusText = "<span style='color:green;font-weight:600;'>OPEN</span>";
            $actionBtn  = "<a class='btn' href='register_event.php?event_id={$e['id']}'>Register</a>";
        } else {
            $statusText = "<span style='color:red;font-weight:600;'>CLOSED</span>";
            $actionBtn  = "<button class='btn' style='background:#6c757d;cursor:not-allowed;' disabled>Closed</button>";
        }

        echo "<tr>
            <td>{$e['event_name']}</td>
            <td>{$e['event_date']}</td>
            <td>{$e['event_time']}</td>
            <td>{$e['venue']}</td>
            <td>$fee</td>
            <td>$statusText</td>
            <td>$rules</td>
            <td>$actionBtn</td>
        </tr>";
    }

} else {
    echo "<tr><td colspan='8' class='empty'>No events available</td></tr>";
}
?>
</table>
</div>

<!-- REGISTERED -->
<div class="section" id="registered">
<h3>📌 My Registrations</h3>

<table>
<tr><th>Event</th><th>Games</th><th>Payment Ref</th></tr>

<?php
$rq = $conn->query("
    SELECT events.event_name, registrations.games_selected, registrations.payment_ref
    FROM registrations
    JOIN events ON registrations.event_id = events.id
    WHERE registrations.student_id='$student_id'
");

if ($rq->num_rows > 0) {
    while ($r = $rq->fetch_assoc()) {
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
