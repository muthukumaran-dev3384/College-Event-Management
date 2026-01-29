<?php
session_start();
include "../db.php";

if (!isset($_SESSION['organizer_id'])) {
    header("Location: login.php");
    exit;
}

$oid = $_SESSION['organizer_id'];
$eid = intval($_GET['id'] ?? 0);
$msg = "";

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
$gamesArr = [];
$gq = $conn->query("SELECT game_name FROM games WHERE event_id='$eid'");
while ($g = $gq->fetch_assoc()) {
    $gamesArr[] = $g['game_name'];
}

/* ---------- UPDATE EVENT ---------- */
if (isset($_POST['update'])) {

    $name  = $_POST['name'];
    $date  = $_POST['date'];
    $time  = $_POST['time'];
    $venue = $_POST['venue'];
    
    $dept  = ($scope=='department') ? $_POST['department'] : 'ALL';

    $feeType = $_POST['fee_type'];
    if ($feeType=='free') {
        $fee = 0;
        $payment = "FREE EVENT";
    } else {
        $fee = $_POST['fee'];
        $payment = $_POST['payment'];
    }

    $conn->query("
        UPDATE events SET
        event_name='$name',
        event_date='$date',
        event_time='$time',
        venue='$venue',
        fee='$fee',
        payment_details='$payment',
        event_scope='$scope',
        department='$dept'
        WHERE id='$eid' AND organizer_id='$oid'
    ");

    $conn->query("DELETE FROM games WHERE event_id='$eid'");
    if (!empty($_POST['games'])) {
        foreach ($_POST['games'] as $game) {
            if ($game!="") {
                $conn->query("
                    INSERT INTO games(event_id, game_name)
                    VALUES('$eid','$game')
                ");
            }
        }
    }

    $msg = "✅ Event Updated Successfully";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Event</title>

<style>
*{box-sizing:border-box;}
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:  
        url("../images/org.jpg") center/cover no-repeat;
    padding:30px;
}
.card{
    max-width:950px;
    margin:auto;
    background:#fff;
    padding:35px;
    border-radius:20px;
    box-shadow:0 15px 35px rgba(0,0,0,0.25);
}
h2{
    text-align:center;
    color:#0d6efd;
    margin-bottom:25px;
}
.form-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:18px;
}
.full{grid-column:1/-1;}

input, select{
    width:100%;
    height:48px;
    padding:10px 14px;
    border-radius:10px;
    border:1px solid #ccc;
    font-size:15px;
}
label{
    font-weight:600;
    color:#333;
}
.radio-group{
    display:flex;
    gap:30px;
    align-items:center;
}
.radio-group input{
    transform:scale(1.1);
}
.box{
    grid-column:1/-1;
    background:#f4f8ff;
    padding:18px;
    border-radius:14px;
}
.games-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:14px;
}
button{
    width:100%;
    height:52px;
    background:#28a745;
    color:#fff;
    border:none;
    border-radius:12px;
    font-size:17px;
    font-weight:600;
    cursor:pointer;
}
button:hover{
    background:#218838;
}
.success{
    text-align:center;
    color:#28a745;
    font-weight:700;
    margin-bottom:15px;
}
.back{
    display:inline-block;
    margin-top:25px;
    padding:12px 26px;
    background:#0d6efd;
    color:#fff;
    text-decoration:none;
    border-radius:10px;
}
.back:hover{
    background:#084298;
}
</style>
</head>

<body>

<div class="card">
<h2>✏️ Edit Event</h2>

<?php if($msg) echo "<div class='success'>$msg</div>"; ?>

<form method="post">
<div class="form-grid">

<input class="full" name="name" value="<?= $e['event_name'] ?>" required placeholder="Event Name">

<input type="date" name="date" value="<?= $e['event_date'] ?>" required>
<input type="time" name="time" value="<?= $e['event_time'] ?>" required>

<input class="full" name="venue" value="<?= $e['venue'] ?>" required placeholder="Venue">



</div>

<div class="box">
<label>Fees Type</label>
<div class="radio-group">
<label><input type="radio" name="fee_type" value="free" <?= ($e['fee']==0)?'checked':'' ?>> Free</label>
<label><input type="radio" name="fee_type" value="paid" <?= ($e['fee']>0)?'checked':'' ?>> Paid</label>
</div>
</div>

<input name="fee" value="<?= $e['fee'] ?>" placeholder="Registration Fee">
<input name="payment" value="<?= $e['payment_details'] ?>" placeholder="Payment Details">

<div class="box">
<label>Games / Activities</label>
<div class="games-grid">
<?php for($i=0;$i<6;$i++): ?>
<input name="games[]" value="<?= $gamesArr[$i] ?? '' ?>" placeholder="Game <?= $i+1 ?>">
<?php endfor; ?>
</div>
</div>

<button class="full" name="update">Update Event</button>

</div>
</form>

<a class="back" href="manage_events.php">⬅ Back to Events</a>
</div>

</body>
</html>
