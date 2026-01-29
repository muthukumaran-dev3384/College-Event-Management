<?php
include "../db.php";

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $q = $conn->query("SELECT * FROM student 
                       WHERE username='$username' 
                       AND password='$password'");

    if ($q->num_rows == 1) {
        $row = $q->fetch_assoc();
        $_SESSION['student_id']   = $row['id'];
        $_SESSION['student_name'] = $row['name'];
        $_SESSION['roll_no']      = $row['roll_no'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid Roll Number or Password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Login</title>

<style>
body{
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family:'Segoe UI',sans-serif;
    background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),url('../images/bg.jpg');
}

.login-box{
    width:380px;
    background:#fff;
    padding:30px;
    border-radius:16px;
    box-shadow:0 25px 45px rgba(0,0,0,0.3);
    animation:fadeUp .6s ease;
}

@keyframes fadeUp{
    from{opacity:0;transform:translateY(40px)}
    to{opacity:1;transform:translateY(0)}
}

.login-box h2{
    text-align:center;
    margin-bottom:25px;
    color:#333;
}

label{
    font-size:14px;
    color:#555;
}

input{
    width:100%;
    padding:12px;
    margin:8px 0 18px;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:15px;
}

input:focus{
    outline:none;
    border-color:#c33764;
}

button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:8px;
    background:#c33764;
    color:#fff;
    font-size:16px;
    cursor:pointer;
    transition:.3s;
}

button:hover{
    background:#a52a55;
}

.error{
    background:#ffe0e0;
    color:#b30000;
    padding:10px;
    border-radius:6px;
    text-align:center;
    margin-bottom:15px;
}

.footer{
    margin-top:15px;
    font-size:13px;
    text-align:center;
    color:#777;
}
</style>
</head>

<body>

<div class="login-box">
    <h2>🎓 Student Login</h2>

    <?php if($error!=""){ ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="post">
        <label>Roll Number</label>
        <input type="text" name="username" placeholder="Enter Roll Number" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter Password" required>

        <button name="login">Login</button>
    </form>

    <div class="footer">
        Default Password: <b>std@123</b>
    </div>
</div>

</body>
</html>
