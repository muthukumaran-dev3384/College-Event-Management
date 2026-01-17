<?php
session_start();
include "../db.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$event_id   = intval($_GET['event_id']);
$student_id = $_SESSION['student_id'];

$error = "";
$success = "";
$limitReached = false;

/* ---------- FETCH EVENT ---------- */
$eventRes = $conn->query("SELECT * FROM events WHERE id='$event_id'");
$event = $eventRes->fetch_assoc();

if (!$event) {
    die("Invalid Event");
}

/* ---------- EVENT STATUS CHECK ---------- */
if ($event['status'] === 'closed') {
    die("<h2 style='text-align:center;color:red;'>❌ Registration Closed for this Event</h2>");
}

/* ---------- FETCH GAMES ---------- */
$games = $conn->query("SELECT * FROM games WHERE event_id='$event_id'");

/* ---------- CHECK EXISTING REGISTRATION ---------- */
$totalGames = 0;
$regRes = $conn->query("
    SELECT games_selected 
    FROM registrations 
    WHERE student_id='$student_id' AND event_id='$event_id'
");

if ($regRes && $regRes->num_rows > 0) {
    while ($row = $regRes->fetch_assoc()) {
        $selected = array_filter(explode(",", $row['games_selected']));
        $totalGames += count($selected);
    }
}

/* ---------- LIMIT CHECK ---------- */
if ($totalGames >= 3) {
    $limitReached = true;
    $error = "🚫 You have already reached the maximum limit (3 games) for this event.";
}

/* ---------- FORM SUBMIT ---------- */
if (isset($_POST['register']) && !$limitReached) {

    if (!isset($_POST['games'])) {
        $error = "Please select at least one game";
    } elseif (($totalGames + count($_POST['games'])) > 3) {
        $error = "⚠ You can register only up to 3 games per event";
    } else {

        $payment = "";
        if ($event['fee'] > 0) {
            if (empty($_POST['payment'])) {
                $error = "Payment reference is required for paid events";
            } else {
                $payment = $conn->real_escape_string($_POST['payment']);
            }
        }

        if (!$error) {
            $game_list = implode(",", $_POST['games']);
            $phone = $conn->real_escape_string($_POST['phone']);
            $email = $conn->real_escape_string($_POST['email']);

            $conn->query("
                INSERT INTO registrations
                (student_id,event_id,games_selected,payment_ref,phone,email)
                VALUES
                ('$student_id','$event_id','$game_list','$payment','$phone','$email')
            ");

            $success = "🎉 Event Registered Successfully";
            $limitReached = true;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register Event</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:linear-gradient(120deg,#667eea,#764ba2);
    padding:40px 15px;
}

.card{
    max-width:780px;
    margin:auto;
    background:#fff;
    padding:35px;
    border-radius:20px;
    box-shadow:0 30px 60px rgba(0,0,0,0.25);
}

h2{
    text-align:center;
    color:#222;
    margin-bottom:25px;
}

/* EVENT INFO */
.event-info{
    background:#f4f6fb;
    padding:18px;
    border-radius:14px;
    margin-bottom:25px;
}
.event-info p{
    margin:6px 0;
    font-size:15px;
}

/* FORM */
form{
    margin-top:15px;
}

.form-group{
    margin-bottom:16px;
}

label{
    font-weight:600;
    display:block;
    margin-bottom:6px;
    color:#333;
}

input[type="text"],
input[type="email"],
input[type="tel"]{
    width:100%;
    padding:13px;
    border-radius:10px;
    border:1px solid #ccc;
    font-size:15px;
}

/* GAMES CHECKBOX GRID */
.games-container{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:12px;
    margin-top:10px;
    padding:10px 0;
}

.games-container label{
    display:flex;
    align-items:center;
    background:#f8f9fa;
    padding:10px 12px;
    border-radius:10px;
    cursor:pointer;
    border:1px solid #e1e1e1;
    transition:0.2s;
}

.games-container label:hover{
    background:#eef3ff;
}

input[type="checkbox"]{
    margin-right:10px;
    transform:scale(1.05);
}

/* BUTTON */
button{
    width:100%;
    margin-top:25px;
    padding:15px;
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

button:disabled{
    background:#aaa;
    cursor:not-allowed;
}

/* ALERTS */
.error{
    background:#ffe3e3;
    color:#b02a37;
    padding:14px;
    border-radius:10px;
    text-align:center;
    font-weight:600;
    margin-top:18px;
}

.success{
    background:#e3fcef;
    color:#0f5132;
    padding:14px;
    border-radius:10px;
    text-align:center;
    font-weight:600;
    margin-top:18px;
}

.warning{
    background:#fff3cd;
    color:#856404;
    padding:14px;
    border-radius:10px;
    text-align:center;
    font-weight:600;
    margin-bottom:18px;
}

/* BACK */
.back{
    display:block;
    text-align:center;
    margin-top:25px;
    font-weight:600;
    text-decoration:none;
    color:#555;
}
.back:hover{color:#007bff;}
</style>
</head>

<body>

<div class="card">

<h2>🎯 Register for Event</h2>

<div class="event-info">
    <p><b>Event:</b> <?= $event['event_name']; ?></p>
    <p><b>Venue:</b> <?= $event['venue']; ?></p>
    <p><b>Fee:</b>
        <?= $event['fee'] == 0 ? "<span style='color:green;font-weight:700;'>FREE</span>" : "₹ ".$event['fee']; ?>
    </p>
    <?php if ($event['fee'] > 0): ?>
        <p><b>Payment No:</b> <?= $event['payment_details']; ?></p>
    <?php endif; ?>
</div>

<?php if ($limitReached): ?>
    <div class="warning">
        🚫 You have already registered for the maximum 3 games in this event.
    </div>
<?php else: ?>

<form method="post">

<h4>Select Games (Max 3)</h4>

<div class="games-container">
<?php while ($g = $games->fetch_assoc()) { ?>
    <label>
        <input type="checkbox" name="games[]" value="<?= $g['game_name']; ?>">
        <?= $g['game_name']; ?>
    </label>
<?php } ?>
</div>

<?php if ($event['fee'] > 0): ?>
<div class="form-group">
    <label>Payment Reference</label>
    <input type="text" name="payment" required>
</div>
<?php endif; ?>

<div class="form-group">
    <label>Phone Number</label>
    <input type="tel" name="phone" required>
</div>

<div class="form-group">
    <label>Email ID</label>
    <input type="email" name="email" required>
</div>

<button name="register">✅ Register Event</button>

</form>
<?php endif; ?>

<?php if($error) echo "<div class='error'>$error</div>"; ?>
<?php if($success) echo "<div class='success'>$success</div>"; ?>

<a href="dashboard.php" class="back">⬅ Back to Dashboard</a>

</div>
</body>
</html>
