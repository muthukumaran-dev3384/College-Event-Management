<?php
include "../db.php";
if (!isset($_SESSION['organizer_id'])) {
    header("Location: login.php");
    exit;
}

/* QUICK STATS */
$eventCount = $conn->query("SELECT COUNT(*) AS total FROM events")->fetch_assoc()['total'];
$regCount   = $conn->query("SELECT COUNT(*) AS total FROM registrations")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Organizer Dashboard</title>

<style>
*{box-sizing:border-box}
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:#f4f6fb;
}

/* LAYOUT */
.wrapper{
    display:flex;
    min-height:100vh;
}

/* SIDEBAR */
.sidebar{
    width:260px;
    background:linear-gradient(180deg,#134e5e,#71b280);
    color:#fff;
    padding:25px;
}

.sidebar h2{
    text-align:center;
    margin-top:0;
}

.profile{
    text-align:center;
    font-size:14px;
    margin-bottom:20px;
    opacity:0.95;
}

.menu a{
    display:block;
    padding:12px 15px;
    margin-bottom:12px;
    background:rgba(255,255,255,0.15);
    color:#fff;
    text-decoration:none;
    border-radius:8px;
    transition:0.3s;
}

.menu a:hover{
    background:rgba(255,255,255,0.3);
}

.logout{
    margin-top:25px;
    display:block;
    background:#dc3545;
    padding:10px;
    text-align:center;
    border-radius:8px;
    text-decoration:none;
    color:#fff;
}

/* MAIN */
.main{
    flex:1;
    padding:30px;
}

/* HEADER */
.header{
    margin-bottom:25px;
}

.header h2{
    margin:0;
    color:#333;
}

/* STATS */
.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    margin-bottom:30px;
}

.stat{
    background:#fff;
    padding:25px;
    border-radius:15px;
    box-shadow:0 12px 30px rgba(0,0,0,0.15);
    text-align:center;
}

.stat h3{
    margin:0;
    font-size:26px;
    color:#007bff;
}

.stat p{
    margin:8px 0 0;
    color:#555;
}

/* CARD */
.card{
    background:#fff;
    padding:25px;
    border-radius:15px;
    box-shadow:0 15px 35px rgba(0,0,0,0.2);
}

.card h3{
    margin-top:0;
    color:#0056b3;
}
</style>

</head>
<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2>🎯 Organizer</h2>

        <div class="profile">
            <?php echo $_SESSION['organizer_name']; ?><br>
            <small>Event Organizer</small>
        </div>

        <div class="menu">
            <a href="dashboard.php">🏠 Dashboard</a>
            <a href="add_event.php">➕ Add Event</a>
            <a href="view_students.php">👥 View Registered Students</a>
            <a href="manage_events.php">📄 View Events</a>
        </div>

        <a href="logout.php" class="logout">🚪 Logout</a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main">

        <div class="header">
            <h2>Welcome, <?php echo $_SESSION['organizer_name']; ?> 👋</h2>
            <p style="color:#555;">Manage your events efficiently</p>
        </div>

        <!-- STATS -->
        <div class="stats">
            <div class="stat">
                <h3><?php echo $eventCount; ?></h3>
                <p>Total Events</p>
            </div>
            <div class="stat">
                <h3><?php echo $regCount; ?></h3>
                <p>Total Registrations</p>
            </div>
        </div>

        <!-- INFO CARD -->
        <div class="card">
            <h3>📌 Organizer Panel</h3>
            <p>
                Use the sidebar to create new events, track student registrations,
                and manage your activities smoothly.
            </p>
        </div>

    </div>
</div>

</body>
</html>
