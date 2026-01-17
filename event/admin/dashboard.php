<?php
include "../db.php";
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

/* ================= COUNTS ================= */
$studentCount   = $conn->query("SELECT COUNT(*) AS total FROM student")->fetch_assoc()['total'];
$organizerCount = $conn->query("SELECT COUNT(*) AS total FROM organizer")->fetch_assoc()['total'];
$eventCount     = $conn->query("SELECT COUNT(*) AS total FROM events")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(120deg, #f2f4f7ff, #ebeff7ff);
            min-height: 100vh;
        }

       .sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;

    background: linear-gradient(180deg, #1e3c72, #2a5298);
    color: #ffffff;

    padding-top: 25px;
    box-shadow: 4px 0 15px rgba(0,0,0,0.25);
    transition: all 0.3s ease;
}

/* SIDEBAR TITLE */
.sidebar h2 {
    text-align: center;
    margin-bottom: 35px;
    font-size: 22px;
    font-weight: 700;
    letter-spacing: 1px;
    color: #ffffff;
}

/* SIDEBAR LINKS */
.sidebar a {
    display: flex;
    align-items: center;
    gap: 12px;

    padding: 14px 25px;
    margin: 6px 12px;

    color: #e6e6e6;
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;

    border-radius: 8px;
    transition: all 0.3s ease;
}

/* ICON STYLE */
.sidebar a i {
    font-size: 16px;
    min-width: 20px;
}

/* HOVER & ACTIVE */
.sidebar a:hover,
.sidebar a.active {
    background: linear-gradient(135deg, #4f46e5, #3b82f6);
    color: #ffffff;
    box-shadow: 0 6px 15px rgba(79,70,229,0.45);
    transform: translateX(5px);
}

/* OPTIONAL SUBTEXT */
.sidebar small {
    display: block;
    font-size: 12px;
    color: #cfd8ff;
    margin-top: 4px;
}

        /* MAIN CONTENT */
        .main {
            margin-left: 240px;
            padding: 30px;
        }

        .card {
            background: #fff;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        /* STATS */
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 25px 0;
        }

        .stat-box {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: #fff;
            padding: 25px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        }

        .stat-box h3 {
            font-size: 32px;
            margin-bottom: 8px;
        }

        .stat-box p {
            font-size: 15px;
            opacity: 0.9;
        }

        /* MENU BUTTONS */
        .menu {
            margin-top: 20px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .menu a {
            background: #f8f9fa;
            padding: 18px;
            border-radius: 14px;
            text-decoration: none;
            font-size: 16px;
            color: #333;
            text-align: center;
            transition: 0.3s;
        }

        .menu a:hover {
            background: #007bff;
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .menu a i {
            display: block;
            font-size: 26px;
            margin-bottom: 8px;
        }

        .footer-note {
            margin-top: 25px;
            text-align: center;
            font-size: 14px;
            color: #555;
        }

        @media(max-width: 900px) {
            .stats {
                grid-template-columns: 1fr;
            }
            .menu {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>ADMIN PANEL</h2>
    <a href="dashboard.php" class="active"><i class="fa fa-home"></i> Dashboard</a>
    <a href="create_students.php"><i class="fa fa-user-graduate"></i> Students</a>
    <a href="create_organizer.php"><i class="fa fa-user-tie"></i> Organizers</a>
    <a href="reports.php"><i class="fa fa-chart-bar"></i> Reports</a>
    <a href="add_notification.php"><i class="fa-chisel fa-regular fa-comment"></i>  Notify</a>
    <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">
    <div class="card">
        <h2> Admin Dashboard</h2>

        <!-- STATS -->
        <div class="stats">
            <div class="stat-box">
                <h3><?= $studentCount ?></h3>
                <p>Registered Students</p>
            </div>
            <div class="stat-box">
                <h3><?= $organizerCount ?></h3>
                <p>Total Organizers</p>
            </div>
            <div class="stat-box">
                <h3><?= $eventCount ?></h3>
                <p>Total Events</p>
            </div>
        </div>

        <!-- QUICK ACTIONS -->


        <div class="footer-note">
            System Status: <b>Active</b> | Last Login: <?= date("d M Y, h:i A") ?>
        </div>
    </div>
</div>

</body>
</html>
