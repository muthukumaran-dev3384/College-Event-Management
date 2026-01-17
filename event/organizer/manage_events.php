<?php
session_start();
include "../db.php";

if (!isset($_SESSION['organizer_id'])) {
    header("Location: login.php");
    exit;
}

$oid = $_SESSION['organizer_id'];

/* ---------- DELETE EVENT ---------- */
if (isset($_GET['delete'])) {
    $eid = intval($_GET['delete']);
    $conn->query("DELETE FROM events WHERE id='$eid' AND organizer_id='$oid'");
    header("Location: manage_events.php");
    exit;
}

/* ---------- TOGGLE STATUS ---------- */
if (isset($_GET['toggle'])) {
    $eid = intval($_GET['toggle']);
    $conn->query("
        UPDATE events 
        SET status = IF(status='open','closed','open')
        WHERE id='$eid' AND organizer_id='$oid'
    ");
    header("Location: manage_events.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Events</title>

<style>
*{box-sizing:border-box}
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65));
    padding:30px;
}
.card{
    max-width:1150px;
    margin:auto;
    background:#fff;
    padding:30px;
    border-radius:16px;
    box-shadow:0 15px 35px rgba(0,0,0,0.15);
}
h2{
    text-align:center;
    color:#0056b3;
    margin-bottom:25px;
}
table{
    width:100%;
    border-collapse:collapse;
    font-size:14px;
}
th, td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid #ddd;
}
th{
    background:#007bff;
    color:#fff;
}
tr:nth-child(even){background:#f4f8ff}

/* BUTTONS */
.btn{
    padding:6px 12px;
    border-radius:6px;
    text-decoration:none;
    font-size:13px;
    color:#fff;
    margin:0 2px;
    display:inline-block;
}
.view{background:#17a2b8}
.edit{background:#28a745}
.delete{background:#dc3545}
.toggle-open{background:#dc3545}
.toggle-closed{background:#28a745}

.btn:hover{opacity:0.85}

/* STATUS BADGE */
.badge{
    padding:5px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
    display:inline-block;
}
.open{background:#e8f5e9;color:#28a745}
.closed{background:#fdecea;color:#dc3545}

.empty{
    text-align:center;
    padding:20px;
    color:#777;
}

.back{
    display:inline-block;
    margin-top:20px;
    padding:12px 22px;
    background:#0d6efd;
    color:#fff;
    text-decoration:none;
    border-radius:8px;
}
.back:hover{background:#084298}
</style>

<script>
function confirmDelete(){
    return confirm("Are you sure you want to delete this event?");
}
</script>

</head>
<body>

<div class="card">
<h2>📋 My Events</h2>

<table>
<tr>
    <th>Event Name</th>
    <th>Date</th>
    <th>Time</th>
    <th>Venue</th>
    <th>Scope</th>
    <th>Fee</th>
    <th>Status</th>
    <th>Actions</th>
</tr>

<?php
$q = $conn->query("
    SELECT * FROM events 
    WHERE organizer_id='$oid'
    ORDER BY event_date DESC
");

if ($q->num_rows > 0) {
    while ($e = $q->fetch_assoc()) {

        $feeText = ($e['fee'] == 0) ? "FREE" : "₹ ".$e['fee'];
        $scopeText = ($e['event_scope'] == 'college')
            ? "College"
            : "Dept - ".$e['department'];

        $statusBadge = ($e['status'] == 'open')
            ? "<span class='badge open'>OPEN</span>"
            : "<span class='badge closed'>CLOSED</span>";

        $toggleBtn = ($e['status'] == 'open')
            ? "<a class='btn toggle-open' href='manage_events.php?toggle={$e['id']}'>Close</a>"
            : "<a class='btn toggle-closed' href='manage_events.php?toggle={$e['id']}'>Reopen</a>";

        echo "<tr>
            <td>{$e['event_name']}</td>
            <td>{$e['event_date']}</td>
            <td>{$e['event_time']}</td>
            <td>{$e['venue']}</td>
            <td>$scopeText</td>
            <td>$feeText</td>
            <td>$statusBadge</td>
            <td>
                <a class='btn view' href='view_event.php?id={$e['id']}'>View</a>
                <a class='btn edit' href='edit_event.php?id={$e['id']}'>Edit</a>
                $toggleBtn
                <a class='btn delete' href='manage_events.php?delete={$e['id']}'
                   onclick='return confirmDelete()'>Delete</a>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='8' class='empty'>No events created yet</td></tr>";
}
?>
</table>

<a href="dashboard.php" class="back">⬅ Back to Dashboard</a>
</div>

</body>
</html>
