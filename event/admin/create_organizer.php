<?php
session_start();
include "../db.php";

/* ---------- AUTH ---------- */
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$msg = $error = "";

/* ---------- ADD ORGANIZER ---------- */
if (isset($_POST['add'])) {

    $department = trim($_POST['department']);
    $username   = trim($_POST['username']);
    $password   = trim($_POST['password']);

    if ($department=="" || $username=="" || $password=="") {
        $error = "All fields are required!";
    } else {

        $check = $conn->query("SELECT id FROM organizer WHERE username='$username'");
        if ($check->num_rows > 0) {
            $error = "Organizer already exists!";
        } else {
            $conn->query("
                INSERT INTO organizer (department, username, password)
                VALUES ('$department', '$username', '$password')
            ");
            header("Location: create_organizer.php?added=1");
            exit;
        }
    }
}

/* ---------- DELETE ---------- */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM organizer WHERE id=$id");
    header("Location: create_organizer.php?deleted=1");
    exit;
}

/* ---------- UPDATE ---------- */
if (isset($_POST['update'])) {

    $id         = intval($_POST['id']);
    $department = trim($_POST['department']);
    $username   = trim($_POST['username']);
    $password   = trim($_POST['password']);

    if ($department=="" || $username=="" || $password=="") {
        $error = "All fields are required!";
    } else {
        $conn->query("
            UPDATE organizer SET
                department='$department',
                username='$username',
                password='$password'
            WHERE id=$id
        ");
        header("Location: create_organizer.php?updated=1");
        exit;
    }
}

/* ---------- STATUS ---------- */
if (isset($_GET['added']))   $msg = "Organizer added successfully!";
if (isset($_GET['updated'])) $msg = "Organizer updated successfully!";
if (isset($_GET['deleted'])) $msg = "Organizer deleted successfully!";
?>

<!DOCTYPE html>
<html>
<head>
<title>Organizer Management</title>

<style>
*{box-sizing:border-box}
body{
    margin:0;
    font-family:'Poppins','Segoe UI',sans-serif;
    background:
        url("../images/adm1.jpg") center/cover no-repeat;
    padding:35px;
}
.container{
    max-width:1150px;
    margin:auto;
}

/* ---------- HEADER ---------- */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
    color:black;
}

/* ---------- BUTTON ---------- */
.btn{
    padding:12px 22px;
    border:none;
    border-radius:12px;
    cursor:pointer;
    font-weight:600;
    transition:.3s;
    text-decoration:none;
    display:inline-block;
}
.btn-add{background:#28a745;color:#fff;}
.btn-edit{background:#0d6efd;color:#fff;}
.btn-del{background:#dc3545;color:#fff;}
.btn-back{background:#343a40;color:#fff;}

.btn:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 25px rgba(0,0,0,.25);
}

/* ---------- CARD ---------- */
.card{
    background:#fff;
    padding:30px;
    border-radius:20px;
    box-shadow:0 25px 50px rgba(0,0,0,.35);
    margin-bottom:30px;
}

/* ---------- FORM ---------- */
.form-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:18px;
}

input{
    padding:13px 15px;
    border-radius:12px;
    border:1px solid #ccc;
    font-size:14px;
}

input:focus{
    outline:none;
    border-color:#6f42c1;
    box-shadow:0 0 0 3px rgba(111,66,193,.25);
}

/* ---------- TABLE ---------- */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
}

th,td{
    padding:14px;
    text-align:center;
}

th{
    background:#212529;
    color:#fff;
}

tr{
    border-bottom:1px solid #ddd;
}

tr:hover{
    background:#f1f4ff;
}

/* ---------- ALERT ---------- */
.msg{
    background:#e6fff0;
    color:#0a7a35;
    padding:14px;
    border-radius:12px;
    margin-bottom:15px;
    font-weight:600;
}

.err{
    background:#ffe5e5;
    color:#b00020;
    padding:14px;
    border-radius:12px;
    margin-bottom:15px;
    font-weight:600;
}

/* ---------- MOBILE ---------- */
@media(max-width:900px){
    .form-grid{grid-template-columns:1fr}
}
</style>
</head>

<body>
<div class="container">

<div class="header">
    <h2>👨‍💼 Organizer Management</h2>
    <a href="dashboard.php" class="btn btn-back">⬅ Dashboard</a>
</div>

<?php if($msg) echo "<div class='msg'>$msg</div>"; ?>
<?php if($error) echo "<div class='err'>$error</div>"; ?>

<!-- ADD ORGANIZER -->
<div class="card">
<h3>➕ Add Organizer</h3>
<form method="post">
<div class="form-grid">
<input name="department" placeholder="Department Name" required>
<input name="username" placeholder="Username" required>
<input name="password" placeholder="Password" required>
</div><br>
<button class="btn btn-add" name="add">Save Organizer</button>
</form>
</div>

<!-- LIST -->
<div class="card">
<h3>📋 Organizer List</h3>

<table>
<tr>
<th>ID</th>
<th>Department</th>
<th>Username</th>
<th>Actions</th>
</tr>

<?php
$res = $conn->query("SELECT * FROM organizer ORDER BY id DESC");
while($row=$res->fetch_assoc()):
?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['department']) ?></td>
<td><?= htmlspecialchars($row['username']) ?></td>
<td>
<a class="btn btn-edit" href="?edit=<?= $row['id'] ?>">Edit</a>
<a class="btn btn-del" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this organizer?')">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</table>
</div>

<!-- EDIT -->
<?php if(isset($_GET['edit'])):
$e = $conn->query("SELECT * FROM organizer WHERE id=".intval($_GET['edit']))->fetch_assoc();
?>
<div class="card">
<h3>✏ Edit Organizer</h3>
<form method="post">
<input type="hidden" name="id" value="<?= $e['id'] ?>">
<div class="form-grid">
<input name="department" value="<?= $e['department'] ?>" required>
<input name="username" value="<?= $e['username'] ?>" required>
<input name="password" value="<?= $e['password'] ?>" required>
</div><br>
<button class="btn btn-edit" name="update">Update Organizer</button>
</form>
</div>
<?php endif; ?>

</div>
</body>
</html>