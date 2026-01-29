<?php
session_start();
include "../db.php";
/* ================= DOWNLOAD HANDLERS ================= */

function downloadCSV($filename, $header, $rows){
    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    $out = fopen('php://output', 'w');
    fputcsv($out, $header);
    foreach($rows as $r){
        fputcsv($out, $r);
    }
    fclose($out);
    exit;
}

/* DEPARTMENT DOWNLOAD */
if(isset($_GET['download_dept'])){
    $dept = $_GET['download_dept'];
    $q = $conn->prepare("SELECT roll_no,name,degree,year FROM student WHERE branch=?");
    $q->bind_param("s",$dept);
    $q->execute();
    $res = $q->get_result();
    $rows=[];
    while($r=$res->fetch_row()) $rows[]=$r;
    downloadCSV("students_$dept.csv",["Roll No","Name","Degree","Year"],$rows);
}

/* ORGANIZER DOWNLOAD */
if(isset($_GET['download_organizer'])){
    $id=$_GET['download_organizer'];
    $q=$conn->query("SELECT event_name,event_date,status FROM events WHERE organizer_id=$id");
    $rows=[];
    while($r=$q->fetch_row()) $rows[]=$r;
    downloadCSV("organizer_events_$id.csv",["Event","Date","Status"],$rows);
}

/* EVENT DOWNLOAD */
if(isset($_GET['download_event'])){
    $id=$_GET['download_event'];
    $q=$conn->prepare("
        SELECT s.roll_no,s.name,r.phone,r.email
        FROM registrations r JOIN student s ON r.student_id=s.id
        WHERE r.event_id=?
    ");
    $q->bind_param("i",$id);
    $q->execute();
    $res=$q->get_result();
    $rows=[];
    while($r=$res->fetch_row()) $rows[]=$r;
    downloadCSV("event_registrations_$id.csv",
        ["Roll No","Name","Phone","Email"],$rows);
}

/* PAYMENT DOWNLOAD */
if(isset($_GET['download_payment'])){
    $ref=$_GET['download_payment'];
    $q=$conn->prepare("
        SELECT s.roll_no,s.name,e.event_name,r.payment_ref
        FROM registrations r
        JOIN student s ON r.student_id=s.id
        JOIN events e ON r.event_id=e.id
        WHERE r.payment_ref=?
    ");
    $q->bind_param("s",$ref);
    $q->execute();
    $res=$q->get_result();
    $rows=[];
    while($r=$res->fetch_row()) $rows[]=$r;
    downloadCSV("payment_$ref.csv",
        ["Roll No","Student","Event","Payment Ref"],$rows);
}


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
/* ================= ORGANIZER AJAX ================= */
if (isset($_GET['organizer'])) {

    $id = (int)$_GET['organizer'];

    /* ========= ORGANIZER DETAILS ========= */
    $stmt = $conn->prepare("
        SELECT  department
        FROM organizer
        WHERE id = ?
    ");

    if (!$stmt) {
        echo "<p style='color:red;'>Organizer SQL Error: {$conn->error}</p>";
        exit;
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $orgRes = $stmt->get_result();

    if ($orgRes->num_rows === 0) {
        echo "<p>No organizer found</p>";
        exit;
    }

    $org = $orgRes->fetch_assoc();

    echo "
    <div style='margin-bottom:20px'>
        <h3 style='color:#0d6efd;'>👨‍💼 Organizer Details</h3>
        <p style='line-height:1.7'>
            <b>Department:</b> ".htmlspecialchars($org['department'])."
        </p>
    </div>

    <h4 style='margin-top:15px;color:#198754;'>📅 Events Created</h4>

    <table class='ajax-table'>
        <tr>
            <th>Event Name</th>
            <th>Event Date</th>
            <th>Event Type</th>
        </tr>
    ";

    /* ========= EVENTS BY ORGANIZER ========= */
    $evStmt = $conn->prepare("
        SELECT event_name, event_date, 
               IF(fee=0,'Free','Paid') AS type
        FROM events
        WHERE organizer_id = ?
        ORDER BY event_date DESC
    ");

    if (!$evStmt) {
        echo "<tr><td colspan='3'>Event SQL Error</td></tr>";
        echo "</table>";
        exit;
    }

    $evStmt->bind_param("i", $id);
    $evStmt->execute();
    $evRes = $evStmt->get_result();

    if ($evRes->num_rows > 0) {
        while ($e = $evRes->fetch_assoc()) {
            echo "<tr>
                <td>".htmlspecialchars($e['event_name'])."</td>
                <td>".htmlspecialchars($e['event_date'])."</td>
                <td>
                    <span style='padding:6px 12px;
                        border-radius:20px;
                        font-weight:600;
                        color:#fff;
                        background:".($e['type']=='Free'?'#0dcaf0':'#198754')."'>
                        {$e['type']}
                    </span>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No events created</td></tr>";
    }

    echo "</table>";
    exit;
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

$payQuery = $conn->query("
    SELECT 
        s.roll_no,
        s.name,
        e.event_name,
        e.fee,
        r.payment_ref
    FROM registrations r
    JOIN student s ON r.student_id = s.id
    JOIN events e ON r.event_id = e.id
    WHERE 
        e.fee > 0
        AND r.payment_ref IS NOT NULL
        AND r.payment_ref <> ''
    ORDER BY r.id DESC
");

$payQuery = $conn->query("
    SELECT 
        s.roll_no,
        s.name,
        e.event_name,
        e.fee,
        r.payment_ref
    FROM registrations r
    JOIN student s ON r.student_id = s.id
    JOIN events e ON r.event_id = e.id
    WHERE e.fee > 0
        AND r.payment_ref IS NOT NULL
        AND r.payment_ref <> ''
    ORDER BY r.id DESC
");

if ($payQuery && $payQuery->num_rows > 0) {
    while ($pay = $payQuery->fetch_assoc()) {

        // ✅ Database already stores relative path
        $filePath = "../" . $pay['payment_ref']; // prepend ../ to reach admin folder

        

        // ✅ Only display if file exists
        if (!empty($pay['payment_ref']) && file_exists($filePath)) {
            echo "
                <a class='btn-view' href='{$filePath}' target='_blank'>👁 View</a>
                <a class='btn-download' href='{$filePath}' download>⬇ Download</a>
            ";
        } else {
          
        }

        echo "</td></tr>";
    }
} else {
    echo "<tr><td colspan='5'>No paid event payments found</td></tr>";
}


/*================= AUTH ================= */
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
/* ===== GLOBAL ===== */
body{
    margin:0;
    font-family:'Segoe UI',system-ui,-apple-system,sans-serif;
    background:
        url("../images/adm1.jpg") center/cover no-repeat;
    padding:30px;
}

/* ===== MAIN CONTAINER ===== */
.dashboard{
    max-width:1400px;
    margin:auto;
    background:#ffffff;
    padding:35px;
    border-radius:20px;
    box-shadow:0 30px 60px rgba(0,0,0,0.28);
}

/* ===== HEADINGS ===== */
h2{
    text-align:center;
    margin-bottom:35px;
    color:#1d2671;
    letter-spacing:0.5px;
}

/* ===== SUMMARY CARDS ===== */
.summary{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:22px;
    margin-bottom:40px;
}
.summary-box{
    background:linear-gradient(135deg,#0d6efd,#003d99);
    color:#fff;
    padding:25px;
    border-radius:18px;
    text-align:center;
    box-shadow:0 12px 25px rgba(13,110,253,0.35);
    transition:transform .25s ease;
}
.summary-box:hover{
    transform:translateY(-6px);
}
.summary-box h3{
    margin:0;
    font-size:34px;
}
.summary-box p{
    margin:6px 0 0;
    font-size:15px;
    opacity:.9;
}

/* ===== REPORT CARD ===== */
.report-card{
    background:#f8faff;
    padding:28px;
    border-radius:18px;
    margin-bottom:35px;
    box-shadow:0 14px 28px rgba(0,0,0,0.14);
}
.report-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:18px;
}
.report-header h3{
    margin:0;
    color:#198754;
}

/* ===== TABLE ===== */
table{
    width:100%;
    border-collapse:separate;
    border-spacing:0;
    overflow:hidden;
    border-radius:14px;
}
th,td{
    padding:14px 12px;
    text-align:center;
}
th{
    background:linear-gradient(135deg,#0d6efd,#084298);
    color:#fff;
    font-size:14px;
    text-transform:uppercase;
}
td{
    border-bottom:1px solid #e4e9f2;
    font-size:14px;
}
tr:nth-child(even){
    background:#f2f6ff;
}
tr:hover{
    background:#e9f0ff;
}

/* ===== ACTION BUTTON GROUP ===== */
.action-btns{
    display:flex;
    gap:12px;
    justify-content:center;
    flex-wrap:wrap;
}

/* ===== BUTTONS ===== */
.btn-view{
    background:linear-gradient(135deg,#0d6efd,#003d99);
    color:#fff;
    padding:8px 16px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:600;
    transition:.25s;
}
.btn-view:hover{
    background:#003d99;
    transform:scale(1.05);
}

.btn-download{
    background:linear-gradient(135deg,#198754,#0f5132);
    color:#fff;
    padding:8px 16px;
    border-radius:8px;
    text-decoration:none;
    font-weight:600;
    transition:.25s;
}
.btn-download:hover{
    background:#0f5132;
    transform:scale(1.05);
}

/* ===== MODAL ===== */
.modal{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.7);
    backdrop-filter:blur(4px);
    justify-content:center;
    align-items:center;
    z-index:999;
}
.modal-content{
    background:#fff;
    width:92%;
    max-width:750px;
    max-height:85vh;
    overflow:auto;
    padding:30px;
    border-radius:16px;
    box-shadow:0 25px 50px rgba(0,0,0,0.35);
    animation:fadeIn .3s ease;
}
@keyframes fadeIn{
    from{opacity:0;transform:translateY(-15px)}
    to{opacity:1;transform:none}
}
.close{
    float:right;
    font-size:24px;
    cursor:pointer;
    color:#dc3545;
    font-weight:bold;
}

/* ===== AJAX TABLE ===== */
.ajax-table th{
    background:linear-gradient(135deg,#198754,#0f5132);
    text-align:left;
}
.ajax-table td{
    text-align:left;
}
.back{
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

/* ===== RESPONSIVE ===== */
@media(max-width:768px){
    body{padding:15px}
    .dashboard{padding:20px}
    th,td{font-size:13px}
    .summary-box h3{font-size:28px}
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
<div class="report-header">
    <h3>👨‍🎓 Department-wise Students</h3>
</div>

<table>
<tr>
    <th>Department</th>
    <th>Total Students</th>
    <th>Action</th>
</tr>

<?php
$q = $conn->query("SELECT branch, COUNT(*) total FROM student GROUP BY branch");
while ($r = $q->fetch_assoc()) {
    echo "<tr>
        <td>{$r['branch']}</td>
        <td>{$r['total']}</td>
        <td>
            <div class='action-btns'>
                <button class='btn-view'
                    onclick=\"ajaxView('?dept={$r['branch']}','Department Students')\">
                    View
                </button>

               <a class='btn-download'
               href='?download_dept={$r['branch']}'>
               Download
              </a>

            </div>
        </td>
    </tr>";
}
?>
</table>
</div>
<!-- ORGANIZER -->
<div class="report-card">
<div class="report-header">
    <h3>👨‍💼 Organizer Report</h3>
</div>

<table>
<tr>
    <th>Organizer Department</th>
    <th>Total Events</th>
    <th>Action</th>
</tr>

<?php
$q = $conn->query("
    SELECT o.id, o.department, COUNT(e.id) AS total
    FROM organizer o
    LEFT JOIN events e ON o.id = e.organizer_id
    GROUP BY o.id
");

if ($q && $q->num_rows > 0) {
    while ($r = $q->fetch_assoc()) {
        echo "<tr>
            <td>{$r['department']}</td>
            <td>{$r['total']}</td>
            <td>
                <div class='action-btns'>
                    <button class='btn-view'
                        onclick=\"ajaxView('?organizer={$r['id']}','Organizer Details')\">
                        View
                    </button>

                    <a class='btn-download'
                        href='?download_organizer={$r['id']}'>
                        Download
                    </a>
                </div>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='3'>No organizers found</td></tr>";
}
?>
</table>
</div>




<!-- EVENT -->
<div class="report-card">
<div class="report-header">
    <h3>🎯 Event Registrations</h3>
</div>

<table>
<tr>
    <th>Event</th>
    <th>Total Registrations</th>
    <th>Action</th>
</tr>

<?php
$q = $conn->query("
    SELECT events.id, events.event_name, COUNT(registrations.id) total
    FROM events
    LEFT JOIN registrations ON events.id = registrations.event_id
    GROUP BY events.id
");

while ($r = $q->fetch_assoc()) {
    echo "<tr>
        <td>{$r['event_name']}</td>
        <td>{$r['total']}</td>
        <td>
            <div class='action-btns'>
                <button class='btn-view'
                    onclick=\"ajaxView('?event={$r['id']}','Event Registrations')\">
                    View
                </button>

                            <a class='btn-download'
                href='?download_event={$r['id']}'>
                Download
                </a>

            </div>
        </td>
    </tr>";
}
?>
</table>
</div>
<!-- PAID PAYMENTS -->
<div class="report-card">
<div class="report-header">
    <h3>💳 Paid Event Payments</h3>
</div>

<table>
<tr>
    <th>Roll No</th>
    <th>Student</th>
    <th>Event</th>
    <th>Amount</th>
    <th>Payment Proof</th>
</tr>

<?php
// Fetch only paid event registrations with uploaded payment proof
$payQuery = $conn->query("
    SELECT 
        s.roll_no,
        s.name,
        e.event_name,
        e.fee,
        r.payment_ref
    FROM registrations r
    JOIN student s ON r.student_id = s.id
    JOIN events e ON r.event_id = e.id
    WHERE e.fee > 0          -- Only paid events
        AND r.payment_ref IS NOT NULL
        AND r.payment_ref <> ''
    ORDER BY r.id DESC
");

// Table rows
if ($payQuery && $payQuery->num_rows > 0) {
    while ($pay = $payQuery->fetch_assoc()) {

        // Build full server file path
        $filePath = "../uploads/payment/" . $pay['payment_ref'];

        echo "<tr>
            <td>".htmlspecialchars($pay['roll_no'])."</td>
            <td>".htmlspecialchars($pay['name'])."</td>
            <td>".htmlspecialchars($pay['event_name'])."</td>
            <td>₹".htmlspecialchars($pay['fee'])."</td>
            <td>";

        // Show View/Download links if file exists
        if (!empty($pay['payment_ref']) && file_exists($filePath)) {
            echo "<a class='btn-view' href='".htmlspecialchars($filePath)."' target='_blank'>👁 View</a>
                  <a class='btn-download' href='".htmlspecialchars($filePath)."' download>⬇ Download</a>";
        } else {
            echo "<span style='color:red;font-weight:600;'>File missing</span>";
        }

        echo "</td></tr>";
    }
} else {
    echo "<tr><td colspan='5'>No paid event payments found</td></tr>";
}
?>
</table>
</div>

</table>
</div>
</div>
  <a href="dashboard.php" class="back">⬅ Back to Dashboard</a>


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
