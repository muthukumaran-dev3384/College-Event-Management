<?php
session_start();
include "../db.php";

if (!isset($_SESSION['organizer_id'])) {
    header("Location: login.php");
    exit;
}

$oid = $_SESSION['organizer_id'];
$msg = "";
$error = "";

if (isset($_POST['add'])) {

    $name  = trim($_POST['name']);
    $date  = $_POST['date'];
    $time  = $_POST['time'];
    $venue = trim($_POST['venue']);

    $scope = $_POST['scope'];
    $department = ($scope === 'department') ? $_POST['department'] : 'ALL';

    $feeType = $_POST['fee_type'];

    if ($feeType === 'free') {
        $fee = 0;
        $payment = "FREE EVENT";
    } else {
        $fee = intval($_POST['fee']);
        $payment = trim($_POST['payment']);

        if ($fee <= 0 || $payment == "") {
            $error = "Paid events must have Fee and Payment Details!";
        }
    }

    $rules_file = "";
    if (isset($_FILES['rules']) && $_FILES['rules']['error'] === 0) {
        $allowed = ['pdf', 'doc', 'docx'];
        $ext = strtolower(pathinfo($_FILES['rules']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $filename = time() . "_" . basename($_FILES['rules']['name']);
            $target = "../uploads/" . $filename;

            if (move_uploaded_file($_FILES['rules']['tmp_name'], $target)) {
                $rules_file = $filename;
            } else {
                $error = "File upload failed!";
            }
        } else {
            $error = "Only PDF, DOC, DOCX files allowed!";
        }
    }

    if (!$error) {

        $sql = "INSERT INTO events
        (organizer_id, event_name, event_date, event_time, venue, fee,
         payment_details, event_scope, department, rules_file)
        VALUES
        ('$oid', '$name', '$date', '$time', '$venue', '$fee',
         '$payment', '$scope', '$department', '$rules_file')";

        if ($conn->query($sql)) {

            $event_id = $conn->insert_id;

            if (!empty($_POST['games'])) {
                foreach ($_POST['games'] as $game) {
                    $game = trim($game);
                    if ($game != "") {
                        $conn->query("INSERT INTO games (event_id, game_name)
                                      VALUES ('$event_id', '$game')");
                    }
                }
            }

            $msg = "🎉 Event Created Successfully!";
        } else {
            $error = "Database Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Event</title>

<style>
*{box-sizing:border-box}
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65));
    padding:30px;
}

.card{
    max-width:980px;
    margin:auto;
    background:#fff;
    padding:35px;
    border-radius:18px;
    box-shadow:0 20px 40px rgba(0,0,0,0.35);
}

h2{
    text-align:center;
    color:#0056b3;
    margin-bottom:25px;
}

.form-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:18px;
}

input, select{
    width:100%;
    height:46px;
    padding:10px 14px;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:14px;
}

input:focus, select:focus{
    outline:none;
    border-color:#007bff;
    box-shadow:0 0 0 3px rgba(0,123,255,0.15);
}

input[type="radio"]{
    transform:scale(0.9);
    margin-right:6px;
}

.full{
    grid-column:1/-1;
}

.scope-box,.fee-box{
    grid-column:1/-1;
    background:#f4f8ff;
    padding:16px;
    border-radius:10px;
}

.scope-row,.fee-row{
    display:flex;
    gap:30px;
    margin-top:8px;
}

.games{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:18px;
    margin-bottom:20px;
}

button{
    width:100%;
    height:50px;
    background:#28a745;
    color:#fff;
    border:none;
    border-radius:10px;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}
button:hover{
    background:#218838;
    transform:translateY(-2px);
}

.success{
    text-align:center;
    color:#28a745;
    font-weight:600;
    margin-bottom:15px;
}
.error{
    text-align:center;
    color:#dc3545;
    font-weight:600;
    margin-bottom:15px;
}

a{
    display:inline-block;
    margin-top:20px;
    padding:12px 22px;
    background:#007bff;
    color:#fff;
    text-decoration:none;
    border-radius:8px;
    font-weight:600;
}
a:hover{
    background:#0056b3;
}

@media(max-width:768px){
    .form-grid{grid-template-columns:1fr}
    .games{grid-template-columns:1fr}
    .scope-row,.fee-row{flex-direction:column;gap:12px}
}
</style>

<script>
function toggleDept(){
    let scope = document.querySelector('input[name="scope"]:checked').value;
    document.getElementById("deptBox").style.display =
        (scope === 'department') ? "block" : "none";
}
function toggleFee(){
    let type = document.querySelector('input[name="fee_type"]:checked').value;
    document.getElementById("feeInput").disabled = (type === 'free');
    document.getElementById("paymentBox").style.display =
        (type === 'free') ? "none" : "block";
}
</script>
</head>

<body>

<div class="card">
<h2>➕ Create New Event</h2>

<?php if($error) echo "<div class='error'>$error</div>"; ?>
<?php if($msg) echo "<div class='success'>$msg</div>"; ?>

<form method="post" enctype="multipart/form-data">

<div class="form-grid">

<input name="name" placeholder="Event Name" required>
<input type="date" name="date" required>

<input type="time" name="time" required>
<input name="venue" placeholder="Venue" required>

<div class="fee-box">
<b>Event Fee</b>
<div class="fee-row">
<label><input type="radio" name="fee_type" value="paid" checked onclick="toggleFee()"> Paid</label>
<label><input type="radio" name="fee_type" value="free" onclick="toggleFee()"> Free</label>
</div>
<input id="feeInput" name="fee" placeholder="Enter Fee">
</div>

<div id="paymentBox" class="full">
<input name="payment" placeholder="UPI / GPay / PhonePe">
</div>

<input type="file" name="rules" class="full">

<div class="scope-box">
<b>Event Scope</b>
<div class="scope-row">
<label><input type="radio" name="scope" value="college" checked onclick="toggleDept()"> College</label>
<label><input type="radio" name="scope" value="department" onclick="toggleDept()"> Department</label>
</div>
<div id="deptBox" style="display:none;margin-top:10px;">
<select name="department">
<option>CS</option>
<option>CT</option>
<option>IT</option>
<option>Chemistry</option>
<option>Civil</option>
<option>Commerce</option>
<option>Maths</option>
</select>
</div>
</div>

</div>

<h4>🎮 Games</h4>
<div class="games">
<input name="games[]" placeholder="Game 1">
<input name="games[]" placeholder="Game 2">
<input name="games[]" placeholder="Game 3">
<input name="games[]" placeholder="Game 4">
<input name="games[]" placeholder="Game 5">
</div>

<button name="add">Create Event</button>
</form>

<a href="dashboard.php">⬅ Back to Dashboard</a>
</div>

</body>
</html>
