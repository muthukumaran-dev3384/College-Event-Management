<?php
session_start();
include "../db.php";

/* ---------- ERROR REPORTING ---------- */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* ---------- AUTH CHECK ---------- */
if (!isset($_SESSION['organizer_id'])) {
    header("Location: login.php");
    exit;
}

$oid = (int)$_SESSION['organizer_id'];

/* ---------- DELETE EVENT (FINAL FIX) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_event'])) {

    $eid = (int)$_POST['event_id'];

    try {
        $conn->begin_transaction();

        // 1️⃣ Delete games FIRST (foreign key)
        $stmt1 = $conn->prepare("DELETE FROM games WHERE event_id = ?");
        $stmt1->bind_param("i", $eid);
        $stmt1->execute();

        // 2️⃣ Delete registrations
        $stmt2 = $conn->prepare("DELETE FROM registrations WHERE event_id = ?");
        $stmt2->bind_param("i", $eid);
        $stmt2->execute();

        // 3️⃣ Delete event
        $stmt3 = $conn->prepare(
            "DELETE FROM events WHERE id = ? AND organizer_id = ?"
        );
        $stmt3->bind_param("ii", $eid, $oid);
        $stmt3->execute();

        $conn->commit();

        header("Location: manage_events.php?deleted=1");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        die("Delete failed: " . $e->getMessage());
    }
}

/* ---------- TOGGLE STATUS ---------- */
if (isset($_GET['toggle'])) {
    $eid = (int)$_GET['toggle'];

    $stmt = $conn->prepare("
        UPDATE events 
        SET status = IF(status='open','closed','open')
        WHERE id = ? AND organizer_id = ?
    ");
    $stmt->bind_param("ii", $eid, $oid);
    $stmt->execute();

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
    background:url("../images/org.jpg") center/cover no-repeat;
    padding:30px;
}
.card{
    max-width:1200px;
    margin:auto;
    background:rgba(255,255,255,0.95);
    padding:35px;
    border-radius:20px;
    box-shadow:0 25px 50px rgba(0,0,0,0.25);
}
h2{
    text-align:center;
    color:#0d6efd;
    margin-bottom:30px;
    font-size:28px;
}
table{
    width:100%;
    border-collapse:collapse;
}
th,td{
    padding:15px;
    text-align:center;
}
th{
    background:#0d6efd;
    color:#fff;
}
tr:nth-child(even){
    background:#f5f9ff;
}
tr:hover{
    background:#eef3ff;
}

/* BUTTONS */
.btn{
    padding:8px 16px;
    border-radius:8px;
    font-size:13px;
    color:#fff;
    border:none;
    cursor:pointer;
    margin:2px;
}
.view{background:#17a2b8}
.edit{background:#198754}
.delete{background:#dc3545}
.toggle-open{background:#dc3545}
.toggle-closed{background:#198754}

.btn:hover{
    opacity:0.9;
    transform:scale(1.05);
}

/* STATUS */
.badge{
    padding:6px 16px;
    border-radius:30px;
    font-size:12px;
    font-weight:700;
}
.open{background:#d1e7dd;color:#0f5132}
.closed{background:#f8d7da;color:#842029}

/* BACK */
.back{
    display:inline-block;
    margin-top:30px;
    padding:14px 28px;
    background:#0d6efd;
    color:#fff;
    text-decoration:none;
    border-radius:10px;
}
.back:hover{background:#084298}
</style>

<script>
function confirmDelete(){
    return confirm("⚠ WARNING!\n\nThis will permanently delete:\n• Event\n• Games\n• Registrations\n\nContinue?");
}
</script>
</head>

<body>

<div class="card">
<h2>📋 My Events</h2>

<table>
<tr>
    <th>Event</th>
    <th>Date</th>
    <th>Time</th>
    <th>Venue</th>
    <th>Fee</th>
    <th>Status</th>
    <th>Actions</th>
</tr>

<?php
$stmt = $conn->prepare("
    SELECT * FROM events
    WHERE organizer_id = ?
    ORDER BY event_date DESC
");
$stmt->bind_param("i", $oid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($e = $result->fetch_assoc()) {

        $fee = ($e['fee'] == 0) ? "FREE" : "₹".$e['fee'];

        $status = ($e['status'] === 'open')
            ? "<span class='badge open'>OPEN</span>"
            : "<span class='badge closed'>CLOSED</span>";

        $toggle = ($e['status'] === 'open')
            ? "<a class='btn toggle-open' href='manage_events.php?toggle={$e['id']}'>Close</a>"
            : "<a class='btn toggle-closed' href='manage_events.php?toggle={$e['id']}'>Reopen</a>";

        echo "<tr>
            <td>{$e['event_name']}</td>
            <td>{$e['event_date']}</td>
            <td>{$e['event_time']}</td>
            <td>{$e['venue']}</td>
            <td>$fee</td>
            <td>$status</td>
            <td>
                <a class='btn view' href='view_event.php?id={$e['id']}'>View</a>
                <a class='btn edit' href='edit_event.php?id={$e['id']}'>Edit</a>
                $toggle
                <form method='post' style='display:inline;' onsubmit='return confirmDelete()'>
                    <input type='hidden' name='event_id' value='{$e['id']}'>
                    <button type='submit' name='delete_event' class='btn delete'>Delete</button>
                </form>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='7'>No events created</td></tr>";
}
?>
</table>

<a href="dashboard.php" class="back">⬅ Back to Dashboard</a>
</div>

</body>
</html>