<?php
session_start();
include "../db.php";

/* ========== AUTH CHECK ========== */
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = (int)$_SESSION['student_id'];

/* ========== FETCH STUDENT ========== */
$stmt = $conn->prepare("SELECT name, roll_no, branch FROM student WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    die("Student not found");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>My Registrations</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;}
body{
    margin:0;
    font-family:'Poppins',Segoe UI,sans-serif;
    background: linear-gradient(135deg,#e0f7fa,#fff);
    padding:30px;
}
.wrapper{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:flex-start;
    padding:20px;
}

/* CARD */
.card{
    width:100%;
    max-width:1200px;
    background:#fff;
    padding:35px;
    border-radius:20px;
    box-shadow:0 20px 40px rgba(0,0,0,.15);
}
h2{
    text-align:center;
    color:#0d3b66;
    margin-top:0;
    margin-bottom:25px;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}
th{
    background:linear-gradient(135deg,#0d6efd,#003d99);
    color:#fff;
    padding:14px;
    font-weight:600;
    text-transform:uppercase;
    font-size:14px;
    border-radius:8px 8px 0 0;
}
td{
    padding:12px;
    border-bottom:1px solid #e4e9f2;
    text-align:center;
    font-size:14px;
}
tr:nth-child(even){background:#f5f8ff;}
tr:hover{background:#e0f2fe;}

/* BADGES */
.badge{
    padding:6px 14px;
    border-radius:20px;
    font-weight:600;
    font-size:13px;
}
.free{background:#0dcaf0;color:#000;}
.paid{background:#198754;color:#fff;}

/* BUTTON */
.btn{
    padding:6px 12px;
    background:linear-gradient(135deg,#0d6efd,#003d99);
    color:#fff;
    border-radius:8px;
    text-decoration:none;
    font-weight:600;
    font-size:13px;
    transition:0.3s;
}
.btn:hover{
    background:linear-gradient(135deg,#003d99,#0d6efd);
    transform:scale(1.05);
}

/* EMPTY ROW */
.empty{
    text-align:center;
    padding:20px;
    font-size:15px;
    color:#555;
}

/* BACK BUTTON */
.back{
    display:inline-block;
    margin-top:20px;
    padding:10px 20px;
   background:#6c757d;
    color:#fff;
    border-radius:10px;
    text-decoration:none;
    font-weight:600;
    transition:0.3s;
}
.back:hover{
    background:#6c757d;
    transform:scale(1.05);
}
</style>
</head>

<body>
<div class="wrapper">
<div class="card">

<h2>📌 My Registered Events</h2>

<table>
<tr>
    <th>Event Name</th>
    <th>Date</th>
    <th>Time</th>
    <th>Venue</th>
    <th>Fee</th>
    <th>Games</th>
    <th>Payment Proof</th>
</tr>

<?php
$sql = "
SELECT 
    e.event_name,
    e.event_date,
    e.event_time,
    e.venue,
    e.fee,
    r.games_selected,
    r.payment_ref
FROM registrations r
JOIN events e ON e.id = r.event_id
WHERE r.student_id = ?
ORDER BY e.event_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {

        // Fee badge
        $feeBadge = ($row['fee'] == 0)
            ? "<span class='badge free'>FREE</span>"
            : "<span class='badge paid'>₹{$row['fee']}</span>";

        // Payment proof
        if ($row['fee'] > 0) {
            if (!empty($row['payment_ref']) && file_exists("../uploads/payment/".$row['payment_ref'])) {
                $payment = "<a class='btn' href='../uploads/payment/".htmlspecialchars($row['payment_ref'])."' download>Download</a>";
            } else {
                $payment = "<span class='badge free'>File missing</span>";
            }
        } else {
            $payment = "<span class='badge free'>FREE</span>";
        }

        echo "<tr>
            <td>".htmlspecialchars($row['event_name'])."</td>
            <td>".htmlspecialchars($row['event_date'])."</td>
            <td>".htmlspecialchars($row['event_time'])."</td>
            <td>".htmlspecialchars($row['venue'])."</td>
            <td>$feeBadge</td>
            <td>".htmlspecialchars($row['games_selected'])."</td>
            <td>$payment</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='7' class='empty'>You have not registered for any events</td></tr>";
}
?>

</table>

<a href="dashboard.php" class="back">⬅ Back to Dashboard</a>

</div>
</div>
</body>
</html>