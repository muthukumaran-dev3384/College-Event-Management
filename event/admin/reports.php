<?php
session_start();
include "../db.php";

/* ================= AJAX HANDLERS ================= */

if (isset($_GET['dept'])) {
    $dept = $_GET['dept'];
    echo "<h3>👨‍🎓 Students – $dept</h3>
    <table class='ajax-table'>
    <tr><th>Roll No</th><th>Name</th><th>Degree</th><th>Year</th></tr>";

    $stmt = $conn->prepare("SELECT roll_no,name,degree,year FROM student WHERE branch=?");
    $stmt->bind_param("s",$dept);
    $stmt->execute();
    $res=$stmt->get_result();

    if($res->num_rows){
        while($r=$res->fetch_assoc()){
            echo "<tr>
            <td>{$r['roll_no']}</td>
            <td>{$r['name']}</td>
            <td>{$r['degree']}</td>
            <td>{$r['year']}</td>
            </tr>";
        }
    } else echo "<tr><td colspan='4'>No students found</td></tr>";
    echo "</table>"; exit;
}

if (isset($_GET['organizer'])) {
    $id=$_GET['organizer'];
    $org=$conn->query("SELECT * FROM organizer WHERE id=$id")->fetch_assoc();

    echo "<h3>👨‍💼 Organizer Details</h3>
    <p><b>Name:</b> {$org['name']}<br>
    <b>Email:</b> {$org['email']}<br>
    <b>Phone:</b> {$org['phone']}</p>

    <h4>Events</h4>
    <table class='ajax-table'>
    <tr><th>Event</th><th>Date</th><th>Status</th></tr>";

    $q=$conn->query("SELECT event_name,event_date,status FROM events WHERE organizer_id=$id");
    while($e=$q->fetch_assoc()){
        echo "<tr>
        <td>{$e['event_name']}</td>
        <td>{$e['event_date']}</td>
        <td>{$e['status']}</td>
        </tr>";
    }
    echo "</table>"; exit;
}

if (isset($_GET['event'])) {
    $id=$_GET['event'];
    echo "<h3>🎯 Registered Students</h3>
    <table class='ajax-table'>
    <tr><th>Roll No</th><th>Name</th><th>Phone</th><th>Email</th></tr>";

    $stmt=$conn->prepare("
        SELECT s.roll_no,s.name,r.phone,r.email
        FROM registrations r JOIN student s ON r.student_id=s.id
        WHERE r.event_id=?
    ");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $res=$stmt->get_result();

    if($res->num_rows){
        while($r=$res->fetch_assoc()){
            echo "<tr>
            <td>{$r['roll_no']}</td>
            <td>{$r['name']}</td>
            <td>{$r['phone']}</td>
            <td>{$r['email']}</td>
            </tr>";
        }
    } else echo "<tr><td colspan='4'>No registrations</td></tr>";
    echo "</table>"; exit;
}

if (isset($_GET['payment'])) {

    $ref = trim($_GET['payment']);

    if ($ref === "") {
        echo "<p style='color:red;'>Invalid Payment Reference</p>";
        exit;
    }

    // Use prepared statement
    $stmt = $conn->prepare("
        SELECT 
            s.roll_no,
            s.name,
            e.event_name,
            r.payment_ref
        FROM registrations AS r
        INNER JOIN student AS s ON r.student_id = s.id
        INNER JOIN events AS e ON r.event_id = e.id
        WHERE r.payment_ref = ?
        LIMIT 1
    ");

    // Check if statement prepared successfully
    if ($stmt === false) {
        echo "<p style='color:red;'>Database error: " . htmlspecialchars($conn->error) . "</p>";
        exit;
    }

    $stmt->bind_param("s", $ref);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p style='color:red;'>No payment record found for reference: {$ref}</p>";
        exit;
    }

    $r = $result->fetch_assoc();

    echo "
    <h3>💳 Payment Details</h3>
    <table style='width:100%;border-collapse:collapse;margin-top:10px'>
        <tr><th style='text-align:left;padding:8px;border:1px solid #ddd;'>Roll No</th><td style='padding:8px;border:1px solid #ddd;'>{$r['roll_no']}</td></tr>
        <tr><th style='text-align:left;padding:8px;border:1px solid #ddd;'>Student Name</th><td style='padding:8px;border:1px solid #ddd;'>{$r['name']}</td></tr>
        <tr><th style='text-align:left;padding:8px;border:1px solid #ddd;'>Event</th><td style='padding:8px;border:1px solid #ddd;'>{$r['event_name']}</td></tr>
        <tr><th style='text-align:left;padding:8px;border:1px solid #ddd;'>Payment Reference</th><td style='padding:8px;border:1px solid #ddd;'>{$r['payment_ref']}</td></tr>
    </table>
    ";

    exit;
}

/* ================= AUTH ================= */
if (!isset($_SESSION['admin'])) {
    header("Location: login.php"); exit;
}

/* COUNTS */
$studentCount=$conn->query("SELECT COUNT(*) t FROM student")->fetch_assoc()['t'];
$organizerCount=$conn->query("SELECT COUNT(*) t FROM organizer")->fetch_assoc()['t'];
$eventCount=$conn->query("SELECT COUNT(*) t FROM events")->fetch_assoc()['t'];
$paymentCount=$conn->query("SELECT COUNT(*) t FROM registrations")->fetch_assoc()['t'];
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Reports</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:linear-gradient(120deg,#1d2b64,#f8cdda);
    padding:30px;
}
.dashboard{
    max-width:1350px;
    margin:auto;
    background:#fff;
    padding:30px;
    border-radius:18px;
    box-shadow:0 25px 45px rgba(0,0,0,0.25);
}
h2{text-align:center;margin-bottom:30px}

/* SUMMARY */
.summary{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:20px;
    margin-bottom:35px;
}
.summary-box{
    background:linear-gradient(135deg,#007bff,#0056b3);
    color:#fff;
    padding:22px;
    border-radius:15px;
    text-align:center;
}
.summary-box h3{margin:0;font-size:32px}

/* REPORT CARD */
.report-card{
    background:#f9fbff;
    padding:25px;
    border-radius:15px;
    margin-bottom:30px;
    box-shadow:0 10px 20px rgba(0,0,0,0.12);
}
.report-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:15px;
}

/* TABLES */
table{
    width:100%;
    border-collapse:collapse;
}
th,td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid #ddd;
}
th{
    background:#0d6efd;
    color:#fff;
}
tr:nth-child(even){background:#f2f6ff}

/* BUTTONS */
.btn-view{
    background:#0d6efd;
    color:#fff;
    padding:7px 14px;
    border:none;
    border-radius:6px;
    cursor:pointer;
}
.download-btn{
    background:#198754;
    color:#fff;
    padding:10px 18px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:600;
}

/* MODAL */
.modal{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.6);
    justify-content:center;
    align-items:center;
}
.modal-content{
    background:#fff;
    width:90%;
    max-width:700px;
    max-height:80vh;
    overflow:auto;
    padding:25px;
    border-radius:12px;
}
.close{
    float:right;
    font-size:22px;
    cursor:pointer;
    color:red;
}

/* AJAX TABLE */
.ajax-table th{
    background:#198754;
}
</style>

<script>
function ajaxView(url,title){
    fetch(url)
    .then(res=>res.text())
    .then(data=>{
        document.getElementById("modalTitle").innerText=title;
        document.getElementById("modalBody").innerHTML=data;
        document.getElementById("modal").style.display="flex";
    });
}
function closeModal(){
    document.getElementById("modal").style.display="none";
}
</script>
</head>

<body>
<div class="dashboard">
<h2>📊 Admin Reports & Analytics</h2>

<div class="summary">
<div class="summary-box"><h3><?= $studentCount ?></h3><p>Students</p></div>
<div class="summary-box"><h3><?= $organizerCount ?></h3><p>Organizers</p></div>
<div class="summary-box"><h3><?= $eventCount ?></h3><p>Events</p></div>
<div class="summary-box"><h3><?= $paymentCount ?></h3><p>Registrations</p></div>
</div>

<!-- DEPARTMENT -->
<div class="report-card">
<div class="report-header"><h3>👨‍🎓 Department-wise Students</h3></div>
<table>
<tr><th>Department</th><th>Total</th><th>View</th></tr>
<?php
$q=$conn->query("SELECT branch,COUNT(*) total FROM student GROUP BY branch");
while($r=$q->fetch_assoc()){
echo "<tr>
<td>{$r['branch']}</td>
<td>{$r['total']}</td>
<td><button class='btn-view' onclick=\"ajaxView('?dept={$r['branch']}','Department Students')\">View</button></td>
</tr>";
}
?>
</table>
</div>

<!-- ORGANIZER -->
<div class="report-card">
<div class="report-header"><h3>👨‍💼 Organizer Report</h3></div>
<table>
<tr><th>Organizer</th><th>Total Events</th><th>View</th></tr>
<?php
$q=$conn->query("SELECT organizer.id,organizer.name,COUNT(events.id) total
FROM organizer LEFT JOIN events ON organizer.id=events.organizer_id GROUP BY organizer.id");
while($r=$q->fetch_assoc()){
echo "<tr>
<td>{$r['name']}</td>
<td>{$r['total']}</td>
<td><button class='btn-view' onclick=\"ajaxView('?organizer={$r['id']}','Organizer Details')\">View</button></td>
</tr>";
}
?>
</table>
</div>

<!-- EVENT -->
<div class="report-card">
<div class="report-header"><h3>🎯 Event Registrations</h3></div>
<table>
<tr><th>Event</th><th>Total</th><th>View</th></tr>
<?php
$q=$conn->query("SELECT events.id,event_name,COUNT(registrations.id) total
FROM events LEFT JOIN registrations ON events.id=registrations.event_id GROUP BY events.id");
while($r=$q->fetch_assoc()){
echo "<tr>
<td>{$r['event_name']}</td>
<td>{$r['total']}</td>
<td><button class='btn-view' onclick=\"ajaxView('?event={$r['id']}','Event Registrations')\">View</button></td>
</tr>";
}
?>
</table>
</div>

<!-- PAYMENT -->
<div class="report-card">
<div class="report-header"><h3>💳 Payment Details</h3></div>
<table>
<tr><th>Roll No</th><th>Student</th><th>Event</th><th>Ref</th><th>View</th></tr>
<?php
$q=$conn->query("SELECT s.roll_no,s.name,e.event_name,r.payment_ref
FROM registrations r JOIN student s ON r.student_id=s.id JOIN events e ON r.event_id=e.id");
while($r=$q->fetch_assoc()){
echo "<tr>
<td>{$r['roll_no']}</td>
<td>{$r['name']}</td>
<td>{$r['event_name']}</td>
<td>{$r['payment_ref']}</td>
<td><button class='btn-view' onclick=\"ajaxView('?payment={$r['payment_ref']}','Payment Details')\">View</button></td>
</tr>";
}
?>
</table>
</div>
</div>

<!-- MODAL -->
<div class="modal" id="modal">
<div class="modal-content">
<span class="close" onclick="closeModal()">✖</span>
<h3 id="modalTitle"></h3>
<hr>
<div id="modalBody"></div>
</div>
</div>

</body>
</html>
