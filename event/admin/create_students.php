<?php
include "../db.php";


if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$msg = $error = "";

/* ---------- CREATE STUDENTS ---------- */
if (isset($_POST['create'])) {

    $degree = trim($_POST['degree']);
    $branch = strtoupper(trim($_POST['branch']));
    $year   = trim($_POST['year']);
    $count  = intval($_POST['count']);

    if ($degree == "" || $branch == "" || $year == "" || $count <= 0) {
        $error = "All fields are required!";
    } else {

        $check = $conn->query("SELECT id FROM student WHERE branch='$branch' AND year='$year'");
        if ($check->num_rows > 0) {
            $error = "Students already exist for this Branch & Year!";
        } else {

            for ($i = 1; $i <= $count; $i++) {

                $roll = "231" . $branch . str_pad($i, 3, "0", STR_PAD_LEFT);

                // ✅ Plain text password (NO HASH)
                $password = "std@123";

                $conn->query("INSERT INTO student 
                (name, roll_no, degree, branch, year, username, password) 
                VALUES 
                ('Student$i', '$roll', '$degree', '$branch', '$year', '$roll', '$password')");
            }

            $msg = "✔ $count Student Logins Generated Successfully!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Student Login</title>

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f2f4f7;
        }

        .card {
            width: 420px;
            background: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .hint {
            background: #f4f8ff;
            padding: 10px;
            border-radius: 8px;
            font-size: 13px;
            color: #444;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 15px;
        }

        .btn {
            width: 100%;
            padding: 13px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: #fff;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .error {
            background: #ffe2e2;
            color: #a10000;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
        }

        .success {
            background: #e6ffea;
            color: #006b21;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
        }

        .preview {
            background: #f1f3f5;
            padding: 10px;
            border-radius: 8px;
            font-size: 14px;
            margin-top: 15px;
            text-align: center;
        }

        a {
            display:inline-block;
            margin-top:20px;
            text-decoration:none;
            padding:12px 22px;
            background:#007bff;
            color:#fff;
            border-radius:8px;
            font-weight:600;
        }
    </style>
</head>

<body>

<div class="card">
    <h2>👨‍🎓 Create Student Login</h2>

    <div class="hint">
        Default Password: <b>std@123</b><br>
        Username = Roll Number
    </div>

    <?php if ($error) echo "<div class='error'>$error</div>"; ?>
    <?php if ($msg) echo "<div class='success'>$msg</div>"; ?>

    <form method="post">
        <div class="form-group">
            <input type="text" name="degree" placeholder="Degree (e.g., BSc)" required>
        </div>

        <div class="form-group">
            <input type="text" name="branch" placeholder="Branch (e.g., CT)" required>
        </div>

        <div class="form-group">
            <input type="text" name="year" placeholder="Year (e.g., 2023)" required>
        </div>

        <div class="form-group">
            <input type="number" name="count" placeholder="Total Students" required>
        </div>

        <button class="btn" name="create">🚀 Generate Student Logins</button>
    </form>

    <div class="preview">
        🔐 Username = Roll Number<br>
        🔑 Password = std@123
    </div>

    <a href="dashboard.php">⬅ Back to Dashboard</a>
</div>

</body>
</html>
