<?php
session_start();
include "../db.php";

/* --------- ADMIN LOGIN CHECK --------- */
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$msg = "";
$error = "";

/* --------- ADD NOTIFICATION --------- */
if (isset($_POST['send'])) {

    $event_id    = $_POST['event_id'] !== "" ? $_POST['event_id'] : NULL;
    $message     = trim($_POST['message']);
    $notify_date = $_POST['notify_date'];

    if ($message == "" || $notify_date == "") {
        $error = "All fields are required!";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO notifications (event_id, message, notify_date)
             VALUES (?, ?, ?)"
        );
        $stmt->bind_param("iss", $event_id, $message, $notify_date);
        $stmt->execute();

        $msg = "✅ Notification Sent Successfully";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Send Notification</title>

<style>
*{box-sizing:border-box}
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:#f2f4f7ff;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}

/* CARD */
.card{
    width:500px;
    background:#fff;
    padding:30px;
    border-radius:18px;
    box-shadow:0 25px 50px rgba(0,0,0,0.3);
}

h2{
    text-align:center;
    color:#0056b3;
    margin-bottom:25px;
}

/* FORM */
label{
    font-size:14px;
    font-weight:600;
    display:block;
    margin-bottom:6px;
}

select, textarea, input{
    width:100%;
    padding:12px;
    margin-bottom:18px;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:14px;
}

textarea{
    resize:none;
    height:90px;
}

input:focus, select:focus, textarea:focus{
    outline:none;
    border-color:#007bff;
}

/* BUTTON */
button{
    width:100%;
    padding:14px;
    background:#28a745;
    border:none;
    color:#fff;
    font-size:16px;
    border-radius:10px;
    cursor:pointer;
    transition:0.3s;
}

button:hover{background:#218838}

/* MESSAGE */
.success{
    margin-top:15px;
    text-align:center;
    color:#28a745;
    font-weight:600;
}

.error{
    margin-top:15px;
    text-align:center;
    color:#dc3545;
    font-weight:600;
}

/* BACK */
a{
    display:inline-block;
    margin-top:20px;
    text-decoration:none;
    padding:12px 22px;
    background:#007bff;
    color:#fff;
    border-radius:8px;
    font-weight:600;
    transition:0.3s;
}
</style>

</head>
<body>

<div class="card">
    <h2>📢 Send Notification</h2>

    <form method="post">

        <label>Event (Optional)</label>
        <select name="event_id">
            <option value="">-- Common Notification (All Students) --</option>
            <?php
            $events = $conn->query("SELECT id, event_name FROM events");
            while ($e = $events->fetch_assoc()) {
                echo "<option value='{$e['id']}'>{$e['event_name']}</option>";
            }
            ?>
        </select>

        <label>Notification Message</label>
        <textarea name="message" placeholder="Enter notification message..." required></textarea>

        <label>Notify Date</label>
        <input type="date" name="notify_date" required>

        <button name="send">Send Notification</button>
    </form>

    <?php if($msg) echo "<div class='success'>$msg</div>"; ?>
    <?php if($error) echo "<div class='error'>$error</div>"; ?>

    <a href="dashboard.php" >⬅ Back to Dashboard</a>
</div>

</body>
</html>
