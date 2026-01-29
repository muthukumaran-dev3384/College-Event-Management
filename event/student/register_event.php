<?php
session_start();
include "../db.php";

/* ================= AUTH CHECK ================= */
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = (int)$_SESSION['student_id'];

/* ================= GET EVENT ID ================= */
if (!isset($_GET['event_id']) || !is_numeric($_GET['event_id'])) {
    die("Invalid Event ID");
}
$event_id = (int)$_GET['event_id'];

$error = "";
$success = "";
$limitReached = false;

/* ================= FETCH EVENT ================= */
$stmt = $conn->prepare("
    SELECT id, event_name, venue, fee, payment_details, status
    FROM events WHERE id = ?
");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) die("Invalid Event");
if ($event['status'] === 'closed') die("<h2 style='text-align:center;color:red;'>❌ Registration Closed</h2>");

/* ================= FETCH GAMES ================= */
$gamesStmt = $conn->prepare("SELECT game_name FROM games WHERE event_id = ?");
$gamesStmt->bind_param("i", $event_id);
$gamesStmt->execute();
$gamesRes = $gamesStmt->get_result();

/* ================= REG CHECK ================= */
$totalGames = 0;
$existingGames = [];

$checkStmt = $conn->prepare("
    SELECT games_selected FROM registrations
    WHERE student_id = ? AND event_id = ?
");
$checkStmt->bind_param("ii", $student_id, $event_id);
$checkStmt->execute();
$checkRes = $checkStmt->get_result();

while ($row = $checkRes->fetch_assoc()) {
    $games = array_filter(explode(",", $row['games_selected']));
    $existingGames = array_merge($existingGames, $games);
    $totalGames += count($games);
}

if ($totalGames >= 3) {
    $limitReached = true;
    $error = "🚫 You already registered for 3 games in this event";
}

/* ================= FORM SUBMIT ================= */
if (isset($_POST['register']) && !$limitReached) {

    if (empty($_POST['games'])) {
        $error = "Please select at least one game";
    }
    else {

        /* 🔴 DUPLICATE GAME CHECK */
        $duplicateGames = array_intersect($existingGames, $_POST['games']);
        if (!empty($duplicateGames)) {
            $error = "❌ You already registered for: " . implode(", ", $duplicateGames);
        }
        elseif (($totalGames + count($_POST['games'])) > 3) {
            $error = "You can register only 3 games per event";
        }
        else {

            $paymentFile = "";

if ($event['fee'] > 0) {

    // ✅ Payment proof is mandatory for paid events
    if (
        !isset($_FILES['payment_ref']) ||
        $_FILES['payment_ref']['error'] !== UPLOAD_ERR_OK
    ) {
        $error = "Payment proof file required";
    } else {

        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $ext = strtolower(pathinfo($_FILES['payment_ref']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $error = "Only JPG, PNG or PDF files are allowed";
        } else {

            // ✅ Ensure upload folder exists
            $uploadDir = "../uploads/payment/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // ✅ Unique & clean filename
            $paymentFile = "PAY_" . $student_id . "_" . $event_id . "_" . time() . "." . $ext;

            // ✅ Final upload path
            $uploadPath = $uploadDir . $paymentFile;

            // ✅ Move file safely
            if (!move_uploaded_file($_FILES['payment_ref']['tmp_name'], $uploadPath)) {
                $error = "Failed to upload payment proof. Try again.";
                $paymentFile = "";
            }
        }
    }
}

            if (!$error) {
                $games_selected = implode(",", $_POST['games']);
                $phone = trim($_POST['phone']);
                $email = trim($_POST['email']);

                $insertStmt = $conn->prepare("
                    INSERT INTO registrations
                    (student_id, event_id, games_selected, payment_ref, phone, email)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");

                $insertStmt->bind_param(
                    "iissss",
                    $student_id,
                    $event_id,
                    $games_selected,
                    $paymentFile,
                    $phone,
                    $email
                );

                if ($insertStmt->execute()) {
                    $success = "🎉 Event Registered Successfully";
                    $limitReached = true;
                } else {
                    $error = "Registration failed. Try again.";
                }
            }
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
    background: white;
    font-family:Poppins,Segoe UI,sans-serif;
    padding:40px;
}
.card{
    max-width:850px;
    margin:auto;
    background:#fff;
    padding:35px;
    border-radius:22px;
    box-shadow:0 30px 70px rgba(0,0,0,.35);
}
h2{text-align:center;color:#1e3c72;margin-bottom:20px}

.event-info{
    background:#f0f5ff;
    padding:18px;
    border-radius:14px;
    margin-bottom:22px;
    line-height:1.8;
}

.form-group{
    margin-top:16px;
}

.games{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:12px;
    margin-top:12px;
}

.game-item{
    background:#eef2ff;
    padding:12px 14px;
    border-radius:10px;
    display:flex;
    align-items:center;
}

.game-item input{
    margin-right:10px;
    transform:scale(1.2);
}

input[type="text"],
input[type="email"],
input[type="tel"],
input[type="file"]{
    width:100%;
    padding:14px;
    border-radius:12px;
    border:1px solid #ccc;
    margin-top:8px;
}

button{
    width:100%;
    padding:15px;
    border-radius:12px;
    border:none;
    font-size:16px;
    margin-top:18px;
    cursor:pointer;
}

button[name="register"]{
    background:#198754;
    color:#fff;
}

.back{
    background:#6c757d;
    color:#fff;
}

.error{
    background:#ffe3e3;
    color:#b02a37;
    padding:14px;
    border-radius:10px;
    margin-top:15px;
}

.success{
    background:#e3fcef;
    color:#0f5132;
    padding:14px;
    border-radius:10px;
    margin-top:15px;
}

.warning{
    background:#fff3cd;
    color:#856404;
    padding:14px;
    border-radius:10px;
}
</style>
</head>

<body>
<div class="card">

<h2>🎯 <?= htmlspecialchars($event['event_name']) ?></h2>

<div class="event-info">
<b>Venue:</b> <?= htmlspecialchars($event['venue']) ?><br>
<b>Fee:</b>
<?= $event['fee']==0 ? "<span style='color:green'>FREE</span>" : "₹ ".$event['fee'] ?><br>
<?php if ($event['fee']>0): ?>
<b>Payment Info:</b> <?= htmlspecialchars($event['payment_details']) ?>
<?php endif; ?>
</div>

<?php if ($limitReached): ?>
<div class="warning">🚫 Maximum game limit is 3 </div>
<?php else: ?>

<form method="post" enctype="multipart/form-data">

<div class="form-group">
<h4>Select Games (Max 3)</h4>
<div class="games">
<?php while ($g = $gamesRes->fetch_assoc()): ?>
<div class="game-item">
<input type="checkbox" name="games[]" value="<?= htmlspecialchars($g['game_name']) ?>">
<?= htmlspecialchars($g['game_name']) ?>
</div>
<?php endwhile; ?>
</div>
</div>

<?php if ($event['fee'] > 0): ?>
<div class="form-group">
<label>Upload Payment Proof</label>
<input type="file" name="payment_ref" required>
</div>
<?php endif; ?>

<div class="form-group">
<input type="tel" name="phone" placeholder="📞 Phone Number" required>
</div>

<div class="form-group">
<input type="email" name="email" placeholder="📧 Email Address" required>
</div>

<button name="register">✅ Register</button>
<button type="button" class="back" onclick="history.back()">⬅ Back</button>

</form>
<?php endif; ?>

<?php if($error) echo "<div class='error'>$error</div>"; ?>
<?php if($success) echo "<div class='success'>$success</div>"; ?>

</div>
</body>
</html>