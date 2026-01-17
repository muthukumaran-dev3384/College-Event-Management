<?php
session_start();
include "../db.php";

if (!isset($_SESSION['organizer_id'])) {
    header("Location: login.php");
    exit;
}

$oid = $_SESSION['organizer_id'];
$eid = intval($_GET['id'] ?? 0);

/* ---------- FETCH EVENT ---------- */
$event = $conn->query("
    SELECT * FROM events 
    WHERE id='$eid' AND organizer_id='$oid'
");

if ($event->num_rows == 0) {
    die("Invalid Event");
}

$e = $event->fetch_assoc();

/* ---------- FETCH GAMES ---------- */
$games = $conn->query("
    SELECT game_name FROM games WHERE event_id='$eid'
");
?>

<!DOCTYPE html>
<html>
<head>
<title>View Event</title>

<style>
*{box-sizing:border-box;}
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:#f4f6fb;
    padding:30px;
}
.card{
    max-width:850px;
    margin:auto;
    background:#fff;
    padding:35px;
    border-radius:20px;
    box-shadow:0 18px 40px rgba(0,0,0,0.25);
}
h2{
    text-align:center;
    color:#0d6efd;
    margin-bottom:30px;
}
.section{
    margin-bottom:22px;
    padding-bottom:15px;
    border-bottom:1px solid #e6e6e6;
}
.row{
    display:flex;
    justify-content:space-between;
    padding:8px 0;
    font-size:15px;
}
.label{
    font-weight:600;
    color:#333;
}
.value{
    color:#555;
}
.badge{
    display:inline-block;
    padding:6px 14px;
    border-radius:20px;
    font-size:13px;
    font-weight:600;
}
.badge-college{background:#e3f2fd;color:#0d6efd;}
.badge-dept{background:#e8f5e9;color:#28a745;}

.games{
    margin-top:10px;
    padding-left:20px;
}
.games li{
    margin:6px 0;
    color:#444;
}

.download{
    color:#0d6efd;
    font-weight:600;
    text-decoration:none;
}
.download:hover{
    text-decoration:underline;
}

.btn-group{
    display:flex;
    justify-content:space-between;
    margin-top:30px;
}
.btn{
    padding:12px 26px;
    border-radius:12px;
    text-decoration:none;
    font-weight:600;
    color:#fff;
}
.back{background:#0d6efd;}
.back:hover{background:#084298;}
.edit{background:#28a745;}
.edit:hover{background:#1e7e34;}
</style>
</head>

<body>

<div class="card">
<h2>📄 Event Details</h2>

<div class="section">
<div class="row">
    <div class="label">Event Name</div>
    <div class="value"><?= $e['event_name'] ?></div>
</div>
<div class="row">
    <div class="label">Date</div>
    <div class="value"><?= $e['event_date'] ?></div>
</div>
<div class="row">
    <div class="label">Time</div>
    <div class="value"><?= $e['event_time'] ?></div>
</div>
<div class="row">
    <div class="label">Venue</div>
    <div class="value"><?= $e['venue'] ?></div>
</div>
</div>

<div class="section">
<div class="row">
    <div class="label">Scope</div>
    <div class="value">
        <?php if($e['event_scope']=='college'){ ?>
            <span class="badge badge-college">College Event</span>
        <?php } else { ?>
            <span class="badge badge-dept"><?= $e['department'] ?> Department</span>
        <?php } ?>
    </div>
</div>

<div class="row">
    <div class="label">Registration Fee</div>
    <div class="value">
        <?= ($e['fee']==0) ? "FREE" : "₹ ".$e['fee']." (".$e['payment_details'].")" ?>
    </div>
</div>
</div>

<div class="section">
<div class="label">Games / Activities</div>
<ul class="games">
<?php
if ($games->num_rows > 0) {
    while ($g = $games->fetch_assoc()) {
        echo "<li>{$g['game_name']}</li>";
    }
} else {
    echo "<li>No games listed</li>";
}
?>
</ul>
</div>

<div class="section">
<div class="row">
    <div class="label">Rules File</div>
    <div class="value">
        <?php if ($e['rules_file']) { ?>
            <a class="download" href="../uploads/<?= $e['rules_file'] ?>" download>📥 Download Rules</a>
        <?php } else { echo "N/A"; } ?>
    </div>
</div>
</div>

<div class="btn-group">
<a href="manage_events.php" class="btn back">⬅ Back</a>
<a href="edit_event.php?id=<?= $eid ?>" class="btn edit">✏️ Edit Event</a>
</div>

</div>

</body>
</html>
