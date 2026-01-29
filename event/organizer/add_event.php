<?php
session_start();
include "../db.php";

/* ---------- AUTH CHECK ---------- */
if (!isset($_SESSION['organizer_id'])) {
    header("Location: login.php");
    exit;
}

$oid = $_SESSION['organizer_id'];
$msg = $error = "";

/* ---------- GET ORGANIZER DEPARTMENT ---------- */
$orgQ = $conn->query("SELECT department FROM organizer WHERE id='$oid'");
$org  = $orgQ->fetch_assoc();
$department = $org['department'] ?? "";

/* ---------- ADD EVENT ---------- */
if (isset($_POST['add'])) {

    $name  = trim($_POST['name']);
    $date  = $_POST['date'];
    $time  = $_POST['time'];
    $venue = trim($_POST['venue']);

    /* ---------- FEE ---------- */
    $feeType = $_POST['fee_type'];

    if ($feeType === "free") {
        $fee = 0;
        $payment = "FREE EVENT";
    } else {
        $fee = intval($_POST['fee']);
        $payment = trim($_POST['payment']);

        if ($fee <= 0 || $payment == "") {
            $error = "Paid events must include Fee and Payment details!";
        }
    }

    /* ---------- RULES FILE ---------- */
    $rules_file = "";
    if (!empty($_FILES['rules']['name'])) {

        $allowed = ['pdf','doc','docx'];
        $ext = strtolower(pathinfo($_FILES['rules']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $error = "Only PDF / DOC / DOCX files allowed!";
        } else {
            $rules_file = time()."_".$_FILES['rules']['name'];
            move_uploaded_file($_FILES['rules']['tmp_name'], "../uploads/".$rules_file);
        }
    }

    /* ---------- INSERT EVENT ---------- */
    if (!$error) {

        $sql = "INSERT INTO events
        (organizer_id, event_name, event_date, event_time, venue,
         fee, payment_details, department, rules_file)
        VALUES
        ('$oid','$name','$date','$time','$venue',
         '$fee','$payment','$department','$rules_file')";

        if ($conn->query($sql)) {

            $event_id = $conn->insert_id;

            /* ---------- GAMES ---------- */
            if (!empty($_POST['games'])) {
                foreach ($_POST['games'] as $game) {
                    if (trim($game) != "") {
                        $conn->query("
                            INSERT INTO games (event_id, game_name)
                            VALUES ('$event_id','".trim($game)."')
                        ");
                    }
                }
            }

            $msg = "🎉 Event successfully created for $department Department!";
        } else {
            $error = "Database Error: ".$conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Create Event</title>

<style>
*{box-sizing:border-box}
body{
    margin:0;
    font-family:'Poppins',sans-serif;
     background:
        url("../images/org.jpg") center/cover no-repeat;
    padding:40px;
}

/* ---------- CARD ---------- */
.card{
    max-width:950px;
    margin:auto;
    background:rgba(255,255,255,0.95);
    padding:40px;
    border-radius:22px;
    box-shadow:0 30px 60px rgba(0,0,0,0.35);
}

/* ---------- TITLE ---------- */
h2{
    text-align:center;
    margin-bottom:10px;
    color:#1d2671;
}
.sub{
    text-align:center;
    color:#555;
    margin-bottom:30px;
    font-weight:600;
}

/* ---------- GRID ---------- */
.form-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:18px;
}

/* ---------- INPUT ---------- */
input,select{
    width:100%;
    height:48px;
    padding:12px 15px;
    border-radius:12px;
    border:1px solid #ccc;
    font-size:14px;
}

input:focus{
    outline:none;
    border-color:#6f42c1;
    box-shadow:0 0 0 3px rgba(111,66,193,0.2);
}

.full{grid-column:1/-1}

/* ---------- SECTION ---------- */
.section{
    grid-column:1/-1;
    background:#f4f6ff;
    padding:20px;
    border-radius:15px;
}

/* ---------- RADIO ---------- */
.row{
    display:flex;
    gap:25px;
    margin-top:10px;
    font-weight:600;
}

/* ---------- GAMES ---------- */
.games{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:16px;
}

/* ---------- BUTTON ---------- */
button{
    width:100%;
    height:55px;
    background:linear-gradient(135deg,#28a745,#20c997);
    color:#fff;
    border:none;
    border-radius:15px;
    font-size:16px;
    font-weight:700;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    transform:translateY(-3px);
    box-shadow:0 15px 30px rgba(0,0,0,0.25);
}

/* ---------- MSG ---------- */
.success{
    background:#e6fff0;
    color:#0a7a35;
    padding:14px;
    border-radius:12px;
    text-align:center;
    font-weight:600;
    margin-bottom:20px;
}

.error{
    background:#ffe5e5;
    color:#b00020;
    padding:14px;
    border-radius:12px;
    text-align:center;
    font-weight:600;
    margin-bottom:20px;
}

/* ---------- LINK ---------- */
a{
    display:inline-block;
    margin-top:25px;
    padding:12px 26px;
    background:#6f42c1;
    color:#fff;
    text-decoration:none;
    border-radius:12px;
    font-weight:600;
}
a:hover{background:#59339d}

/* ---------- MOBILE ---------- */
@media(max-width:768px){
    .form-grid,.games{grid-template-columns:1fr}
    body{padding:20px}
}
</style>

<script>
function toggleFee(){
    let type = document.querySelector('input[name="fee_type"]:checked').value;
    document.getElementById("fee").disabled = (type === "free");
    document.getElementById("paymentBox").style.display =
        (type === "free") ? "none" : "block";
}
</script>

</head>

<body>

<div class="card">

<h2>➕ Create Event</h2>
<div class="sub">Department : <b><?= htmlspecialchars($department) ?></b></div>

<?php if($error) echo "<div class='error'>$error</div>"; ?>
<?php if($msg)   echo "<div class='success'>$msg</div>"; ?>

<form method="post" enctype="multipart/form-data">

<div class="form-grid">

<input name="name" placeholder="Event Name" required>
<input type="date" name="date" required>

<input type="time" name="time" required>
<input name="venue" placeholder="Venue" required>

<div class="section">
<b>Event Fee</b>
<div class="row">
<label><input type="radio" name="fee_type" value="paid" checked onclick="toggleFee()"> Paid</label>
<label><input type="radio" name="fee_type" value="free" onclick="toggleFee()"> Free</label>
</div>
<input id="fee" name="fee" placeholder="Enter Fee">
</div>

<div id="paymentBox" class="full">
<input name="payment" placeholder="UPI / GPay / PhonePe">
</div>

<b class="full">Rules (Optional)</b>
<input type="file" name="rules" class="full">

</div>

<h4>🎮 Games / Events</h4>
<div class="games">
<input name="games[]" placeholder="Game 1">
<input name="games[]" placeholder="Game 2">
<input name="games[]" placeholder="Game 3">
<input name="games[]" placeholder="Game 4">
</div>

<br>
<button name="add">Create Event</button>

</form>

<a href="dashboard.php">⬅ Back to Dashboard</a>

</div>

</body>
</html>