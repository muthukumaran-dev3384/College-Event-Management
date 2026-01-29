<?php
session_start();
include "../db.php";

/* ========== AUTH CHECK ========== */
if (!isset($_SESSION['organizer_id'])) {
    header("Location: login.php");
    exit;
}

$oid = (int)$_SESSION['organizer_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Registered Students</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',Segoe UI,sans-serif;}
body{
    min-height:100vh;
 background:  
        url("../images/org.jpg") center/cover no-repeat;
    display:flex;
    justify-content:center;
    padding:20px;
}

/* CARD */
.card{
    width:100%;
    max-width:1200px;
    background:rgba(255,255,255,0.1);
    backdrop-filter:blur(15px);
    border-radius:20px;
    padding:30px;
    box-shadow:0 20px 50px rgba(0,0,0,0.4);
    color:#fff;
}

/* TITLE */
.card h2{
    text-align:center;
    margin-bottom:25px;
    font-size:28px;
    letter-spacing:1px;
    color:#ffd700;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    border-radius:12px;
    overflow:hidden;
}
th, td{
    padding:14px 12px;
    text-align:center;
    font-size:14px;
}
th{
    background:linear-gradient(135deg,#ff9800,#ff5722);
    color:#fff;
    font-weight:600;
}
tr:nth-child(even){background:rgba(255,255,255,0.05);}
tr:nth-child(odd){background:rgba(255,255,255,0.1);}
tr:hover{background:rgba(255,255,255,0.2);transition:0.3s;}

/* BADGES */
.badge{
    padding:5px 12px;
    border-radius:20px;
    font-weight:600;
    font-size:12px;
}
.free{background:#0dcaf0;color:#000;}
.paid{background:#198754;color:#fff;}
.missing{background:#dc3545;color:#fff;}

/* DOWNLOAD BUTTON */
.download-btn{
    display:inline-block;
    padding:6px 12px;
    background:linear-gradient(135deg,#28a745,#1e7e34);
    color:#fff;
    border-radius:8px;
    text-decoration:none;
    font-weight:600;
    transition:0.3s;
}
.download-btn:hover{
    background:linear-gradient(135deg,#1e7e34,#28a745);
    transform:scale(1.05);
}

/* BACK BUTTON */
a.back{
    display:inline-block;
    margin-top:20px;
    padding:12px 22px;
    background:#0d6efd;
    color:#fff;
    border-radius:10px;
    text-decoration:none;
    font-weight:600;
    transition:0.3s;
}
a.back:hover{
    transform:scale(1.05);
}

/* RESPONSIVE */
@media(max-width:768px){
    table th, table td{padding:10px;font-size:13px;}
    .card h2{font-size:22px;}
}
</style>
</head>
<body>

<div class="card">
    <h2>📋 Registered Students</h2>

    <table>
        <tr>
            <th>Event</th>
            <th>Name</th>
            <th>Roll No</th>
            <th>Games</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Payment</th>
        </tr>

        <?php
        $stmt = $conn->prepare("
            SELECT 
                e.event_name,
                e.fee,
                r.games_selected,
                r.phone,
                r.email,
                r.payment_ref,
                s.name,
                s.roll_no
            FROM registrations r
            JOIN events e ON r.event_id = e.id
            JOIN student s ON r.student_id = s.id
            WHERE e.organizer_id = ?
            ORDER BY e.event_date DESC
        ");
        $stmt->bind_param("i",$oid);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                
                // Payment column logic
                if($row['fee'] == 0){
                    $payment = "<span class='badge free'>FREE</span>";
                } else {
                    $filePath = "../uploads/payment/".$row['payment_ref'];
                    if(!empty($row['payment_ref']) && file_exists($filePath)){
                        $payment = "<a class='download-btn' href='{$filePath}' download>Download</a>";
                    } else {
                        $payment = "<span class='badge missing'>File missing</span>";
                    }
                }

                echo "<tr>
                    <td>".htmlspecialchars($row['event_name'])."</td>
                    <td>".htmlspecialchars($row['name'])."</td>
                    <td>".htmlspecialchars($row['roll_no'])."</td>
                    <td>".htmlspecialchars($row['games_selected'])."</td>
                    <td>".htmlspecialchars($row['phone'])."</td>
                    <td>".htmlspecialchars($row['email'])."</td>
                    <td>$payment</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7' class='empty'>No registrations found</td></tr>";
        }
        ?>
    </table>

    <a href="dashboard.php" class="back">⬅ Back to Dashboard</a>
</div>

</body>
</html>