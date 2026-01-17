<?php
include "../db.php";


if (!isset($_SESSION['organizer_id'])) {
    header("Location: login.php");
    exit;
}

$oid = $_SESSION['organizer_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Registered Students</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI', Arial, sans-serif;
}

/* BACKGROUND */
body{
    min-height:100vh;
    background:
        linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)),
        url("../assets/images/bg.jpg") center/cover no-repeat;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:20px;
}

/* CARD */
.card{
    width:100%;
    max-width:1150px;
    background:rgba(255,255,255,0.12);
    backdrop-filter:blur(14px);
    border-radius:16px;
    padding:30px;
    box-shadow:0 20px 40px rgba(0,0,0,0.4);
    color:#fff;
}

/* TITLE */
.card h2{
    text-align:center;
    margin-bottom:25px;
    font-size:28px;
    letter-spacing:1px;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    overflow:hidden;
    border-radius:12px;
}

table th, table td{
    padding:14px 12px;
    text-align:center;
    font-size:14px;
}

table th{
    background:#ff9800;
    color:#fff;
    font-weight:600;
}

table tr:nth-child(even){
    background:rgba(255,255,255,0.08);
}

table tr:nth-child(odd){
    background:rgba(255,255,255,0.03);
}

table tr:hover{
    background:rgba(255,255,255,0.15);
    transition:0.3s;
}

/* BACK BUTTON */
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

a:hover{
    background:#0056b3;
    transform:translateY(-2px);
}

/* RESPONSIVE */
@media(max-width:768px){
    table{
        font-size:13px;
    }

    table th, table td{
        padding:10px;
    }

    .card h2{
        font-size:22px;
    }
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
            <th>Payment Ref</th>
        </tr>

        <?php
        $q = $conn->query("
            SELECT 
                events.event_name,
                student.name,
                student.roll_no,
                registrations.games_selected,
                registrations.phone,
                registrations.email,
                registrations.payment_ref
            FROM events
            JOIN registrations ON events.id = registrations.event_id
            JOIN student ON registrations.student_id = student.id
            WHERE events.organizer_id = '$oid'
        ");

        if ($q->num_rows > 0) {
            while ($r = $q->fetch_assoc()) {
                echo "<tr>
                    <td>{$r['event_name']}</td>
                    <td>{$r['name']}</td>
                    <td>{$r['roll_no']}</td>
                    <td>{$r['games_selected']}</td>
                    <td>{$r['phone']}</td>
                    <td>{$r['email']}</td>
                    <td>{$r['payment_ref']}</td>
                </tr>";
            }
        } else {
            echo "<tr>
                <td colspan='7' style='padding:20px;'>No registrations found</td>
            </tr>";
        }
        ?>
    </table>

    <a href="dashboard.php">⬅ Back to Dashboard</a>
</div>

</body>
</html>
