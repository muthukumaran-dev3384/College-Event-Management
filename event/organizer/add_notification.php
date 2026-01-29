<?php
session_start();
include "../db.php";

/* ================= AUTH CHECK ================= */
if (!isset($_SESSION['organizer_id'])) {
    header("Location: login.php");
    exit;
}

$organizer_id = (int)$_SESSION['organizer_id'];
$msg = "";
$error = "";

/* ================= FETCH ORGANIZER EVENTS ================= */
$eventStmt = $conn->prepare(
    "SELECT id, event_name, department 
     FROM events 
     WHERE organizer_id = ?"
);

if (!$eventStmt) {
    die("Prepare failed (events): " . $conn->error);
}

$eventStmt->bind_param("i", $organizer_id);
$eventStmt->execute();
$eventsResult = $eventStmt->get_result();

/* ================= SEND NOTIFICATION ================= */
if (isset($_POST['send'])) {

    $event_id    = (int)$_POST['event_id'];
    $message     = trim($_POST['message']);
    $notify_date = $_POST['notify_date'];

    if ($event_id <= 0 || $message === "" || $notify_date === "") {
        $error = "All fields are required!";
    } else {

        $stmt = $conn->prepare(
            "INSERT INTO notifications (event_id, message, notify_date)
             VALUES (?, ?, ?)"
        );

        if (!$stmt) {
            die("Prepare failed (notification): " . $conn->error);
        }

        $stmt->bind_param("iss", $event_id, $message, $notify_date);
        $stmt->execute();
        $stmt->close();

        $msg = "✅ Notification sent successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Send Event Notification</title>

<style>
*{box-sizing:border-box}
body{
    margin:0;
    font-family:'Poppins','Segoe UI',sans-serif;
     background:url("../images/org.jpg") center/cover no-repeat;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}
.card{
    width:560px;
    background:#fff;
    padding:38px;
    border-radius:24px;
    box-shadow:0 28px 60px rgba(0,0,0,.35);
}
h2{
    text-align:center;
    color:#1d2671;
    margin-top:0;
}
label{
    font-weight:600;
    display:block;
    margin-bottom:8px;
}
select, textarea, input{
    width:100%;
    padding:14px;
    border-radius:14px;
    border:1px solid #ccc;
    font-size:14px;
}
textarea{
    resize:none;
    height:120px;
}
button{
    width:100%;
    margin-top:24px;
    padding:15px;
    background:#28a745;
    border:none;
    color:#fff;
    font-size:16px;
    font-weight:600;
    border-radius:16px;
    cursor:pointer;
}
button:hover{background:#218838}
.success{
    margin-top:18px;
    text-align:center;
    color:#28a745;
    font-weight:600;
}
.error{
    margin-top:18px;
    text-align:center;
    color:#dc3545;
    font-weight:600;
}
a{
    display:block;
    margin-top:24px;
    text-align:center;
    padding:12px;
    background:#0d6efd;
    color:#fff;
    border-radius:14px;
    text-decoration:none;
    font-weight:600;
}
a:hover{background:#084298}
</style>
</head>

<body>

<div class="card">
    <h2>📢 Send Event Notification</h2>

    <form method="post">

        <label>Select Event</label>
        <select name="event_id" required>
            <option value="">-- Select Event --</option>
            <?php while ($e = $eventsResult->fetch_assoc()): ?>
                <option value="<?= $e['id'] ?>">
                    <?= htmlspecialchars($e['event_name']) ?> (<?= $e['department'] ?>)
                </option>
            <?php endwhile; ?>
        </select>

        <label>Notification Message</label>
        <textarea name="message" placeholder="Enter announcement..." required></textarea>

        <label>Notify Date</label>
        <input type="date" name="notify_date" required>

        <button type="submit" name="send">Send Notification</button>
    </form>

    <?php if ($msg)   echo "<div class='success'>$msg</div>"; ?>
    <?php if ($error) echo "<div class='error'>$error</div>"; ?>

    <a href="dashboard.php">⬅ Back to Dashboard</a>
</div>

</body>
</html>
