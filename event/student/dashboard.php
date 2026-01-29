<?php
session_start();
include "../db.php";

/* ================= AUTH CHECK ================= */
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = (int)$_SESSION['student_id'];

/* ================= FETCH STUDENT ================= */
$stmt = $conn->prepare(
    "SELECT name, roll_no, branch FROM student WHERE id=?"
);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    die("Student not found");
}

$student_name = $student['name'];
$student_roll = $student['roll_no'];
$student_dept = strtoupper(trim($student['branch'])); // student department
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard - Notifications</title>
<style>
*{box-sizing:border-box;}
body{
    margin:0;
    font-family:Poppins,Segoe UI,sans-serif;
    background:linear-gradient(135deg,#1d2671,#c33764);
}
.wrapper{display:flex;min-height:100vh;}

/* ===== SIDEBAR ===== */
.sidebar{
    width:260px;
    background:#0b1c2d;
    color:#fff;
    padding:25px;
    display:flex;
    flex-direction:column;
}
.sidebar h2{text-align:center;margin-bottom:20px;font-size:22px;}
.info{
    text-align:center;
    font-size:14px;
    margin-bottom:25px;
    line-height:1.6;
}
.menu a{
    display:block;
    padding:14px;
    margin-bottom:12px;
    background:#1c2f45;
    color:#fff;
    text-decoration:none;
    border-radius:10px;
    transition:.3s;
}
.menu a:hover{
    background:#355c7d;
    transform:translateX(5px);
}
.logout{
    display:block;
    margin-top:auto;
    text-align:center;
    background:#dc3545;
    padding:12px;
    border-radius:10px;
    color:#fff;
    text-decoration:none;
    font-weight:600;
}

/* ===== MAIN CONTENT ===== */
.main{
    flex:1;
    padding:40px;
    background:#f4f7fb;
}
.card{
    background:#fff;
    padding:30px;
    border-radius:20px;
    box-shadow:0 25px 50px rgba(0,0,0,.15);
    margin-bottom:25px;
}
.card h2, .card h3{
    color:#1d2671;
    margin-top:0;
}
.card p{color:#555;font-size:16px;line-height:1.5;}

/* ===== NOTIFICATIONS ===== */
.notify{
    background:#eef4ff;
    border-left:5px solid #0d6efd;
    padding:18px 20px;
    margin-bottom:15px;
    border-radius:12px;
    box-shadow:0 4px 15px rgba(0,0,0,.05);
    transition:.2s;
    display:flex;
    align-items:flex-start;
    gap:10px;
}
.notify:hover{
    background:#e0ebff;
}
.notify-icon{
    font-size:22px;
    margin-top:3px;
}
.notify-content b{
    color:#0d6efd;
    font-weight:600;
}
.notify-content i{
    font-style:italic;
    color:#333;
}
.empty{
    text-align:center;
    color:#777;
    padding:25px;
    font-size:15px;
}

/* ===== BACK BUTTON ===== */
.back{
    display:inline-block;
    margin-top:20px;
    padding:10px 18px;
    background:#6c757d;
    color:#fff;
    border-radius:10px;
    text-decoration:none;
    font-weight:600;
    transition:.3s;
}
.back:hover{background:#495057;}
</style>
</head>

<body>
<div class="wrapper">

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>🎓 Student</h2>
    <div class="info">
        <b><?= htmlspecialchars($student_name) ?></b><br>
        Roll: <?= htmlspecialchars($student_roll) ?><br>
        Dept: <?= htmlspecialchars($student_dept) ?>
    </div>

    <div class="menu">
        <a href="dashboard.php">🔔 Notifications</a>
        <a href="free_event.php">🆓 Free Events</a>
        <a href="paid_event.php">💳 Paid Events</a>
        <a href="myregistration.php">📌 My Registrations</a>
        
    </div>

    <a href="logout.php" class="logout">Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">
    <div class="card">
        <h2>📢 Department Notifications</h2>

        <?php
        /* ================= FETCH DEPARTMENT NOTIFICATIONS ================= */
        // Assuming 'events' table has a 'department' column
        $sql = "
        SELECT n.event_name, n.message, n.notify_date
        FROM notifications n
        JOIN events e ON n.event_id = e.id
        WHERE UPPER(e.department) = ?
        ORDER BY n.notify_date DESC
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Notification SQL Error: " . $conn->error);
        }

        $stmt->bind_param("s", $student_dept);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            while ($n = $res->fetch_assoc()) {
                echo "<div class='notify'>
                        <div class='notify-icon'>📌</div>
                        <div class='notify-content'>
                            <b>".htmlspecialchars($n['notify_date'])." - ".htmlspecialchars($n['event_name'])."</b><br>
                            <i>".htmlspecialchars($n['message'])."</i>
                        </div>
                      </div>";
            }
        } else {
            echo "<div class='empty'>No notifications available for your department</div>";
        }
        ?>
    </div>
</div>

</div>
</body>
</html>