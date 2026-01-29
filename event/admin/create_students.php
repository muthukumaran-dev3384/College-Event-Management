<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$msg = $error = "";

/* ================= CSV UPLOAD ================= */
if (isset($_POST['upload'])) {

    if (empty($_FILES['csv']['name'])) {
        $error = "Please select CSV file";
    } else {

        $file = fopen($_FILES['csv']['tmp_name'], "r");

        /* GET LAST SERIAL NUMBER */
        $snRes = $conn->query("SELECT IFNULL(MAX(serial_no),0) AS sn FROM student");
        $sn    = $snRes->fetch_assoc()['sn'];

        $count = 0;
        $row   = 0;

        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {

            /* SKIP HEADER */
            if ($row === 0) {
                $row++;
                continue;
            }

            /*
            EXPECTED CSV FORMAT
            -------------------
            0 = serial_no (IGNORE)
            1 = name
            2 = roll_no
            3 = degree
            4 = branch
            5 = year
            */

            $name   = trim($data[1] ?? '');
            $roll   = trim($data[2] ?? '');
            $degree = trim($data[3] ?? '');
            $branch = strtoupper(trim($data[4] ?? ''));
            $year   = trim($data[5] ?? '');

            if ($roll === '') continue;

            $username = $roll;
            $password = "std@123"; // ✅ PLAIN TEXT PASSWORD

            $sn++;

            $stmt = $conn->prepare("
                INSERT IGNORE INTO student
                (serial_no, name, roll_no, degree, branch, year, username, password)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param(
                "isssssss",
                $sn,
                $name,
                $roll,
                $degree,
                $branch,
                $year,
                $username,
                $password
            );

            if ($stmt->execute()) {
                $count++;
            }
        }

        fclose($file);
        $msg = "✔ $count Students Uploaded Successfully";
    }
}



/* ================= FETCH BY DEPARTMENT ================= */
$branches = $conn->query("
    SELECT DISTINCT branch 
    FROM student 
    ORDER BY branch
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Details</title>

<style>
body{
    font-family:'Segoe UI',sans-serif;
    background:
        url("../images/adm1.jpg") center/cover no-repeat;
    padding:30px;
}
.container{max-width:1300px;margin:auto;}
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}
h2{margin:0;}
.card{
    background:#fff;
    padding:25px;
    border-radius:18px;
    box-shadow:0 15px 30px rgba(0,0,0,.12);
    margin-bottom:25px;
}
.btn{
    padding:10px 20px;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-weight:600;
}
.upload{background:#0d6efd;color:#fff;}
.back{background:#6c757d;color:#fff;text-decoration:none;}
input{
    padding:10px;
    border-radius:10px;
    border:1px solid #ccc;
}
table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
}
th,td{
    padding:12px;
    border-bottom:1px solid #e3e3e3;
    text-align:center;
}
th{
    background:#212529;
    color:#fff;
}
tr:hover{background:#f6f9ff;}
.msg{background:#e6ffea;padding:12px;border-radius:10px;margin-bottom:15px;}
.err{background:#ffe2e2;padding:12px;border-radius:10px;margin-bottom:15px;}
.dept-title{
    font-size:20px;
    font-weight:700;
    color:#0d6efd;
    margin-bottom:10px;
}
</style>
</head>

<body>
<div class="container">

<div class="header">
    <h2>🎓 Student Management</h2>
    <a href="dashboard.php" class="btn back">⬅ Back to Dashboard</a>
</div>

<?php if($msg) echo "<div class='msg'>$msg</div>"; ?>
<?php if($error) echo "<div class='err'>$error</div>"; ?>

<!-- UPLOAD -->
<div class="card">
<h3>📤 Upload Student CSV</h3>
<form method="post" enctype="multipart/form-data">
<input type="file" name="csv" accept=".csv" required>
<button class="btn upload" name="upload">Upload CSV</button>
</form>
</div>

<!-- DEPARTMENT WISE DISPLAY -->
<?php while($b = $branches->fetch_assoc()): 
    $branch = $b['branch'];
    $students = $conn->query("
        SELECT * FROM student 
        WHERE branch='$branch'
        ORDER BY year, serial_no
    ");
?>
<div class="card">
<div class="dept-title">📘 Department : <?= $branch ?></div>

<table>
<tr>
<th>SN</th>
<th>Name</th>
<th>Roll No</th>
<th>Degree</th>
<th>Year</th>
</tr>

<?php if($students->num_rows): while($s=$students->fetch_assoc()): ?>
<tr>
<td><?= $s['serial_no'] ?></td>
<td><?= $s['name'] ?></td>
<td><?= $s['roll_no'] ?></td>
<td><?= $s['degree'] ?></td>
<td><?= $s['year'] ?></td>
</tr>
<?php endwhile; else: ?>
<tr><td colspan="5">No students found</td></tr>
<?php endif; ?>
</table>
</div>
<?php endwhile; ?>

</div>
</body>
</html>