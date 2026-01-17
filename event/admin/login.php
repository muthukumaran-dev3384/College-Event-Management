<?php
include "../db.php";
$error = "";

if (isset($_POST['login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];

    $q = $conn->query("SELECT * FROM admin 
                       WHERE username='$u' AND password='$p'");
    if ($q->num_rows == 1) {
        $_SESSION['admin'] = $u;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid Username or Password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>

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
    display:flex;
    align-items:center;
    justify-content:center;
    background:
        linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
        url("../assets/images/bg.jpg") center/cover no-repeat;
}

/* LOGIN CARD */
.card{
    width:380px;
    background:rgba(255,255,255,0.15);
    backdrop-filter:blur(15px);
    padding:35px;
    border-radius:16px;
    box-shadow:0 20px 45px rgba(0,0,0,0.4);
    text-align:center;
    color:#fff;
}

/* TITLE */
.card h2{
    margin-bottom:25px;
    font-size:26px;
    letter-spacing:1px;
}

/* INPUT */
.card input{
    width:100%;
    padding:14px;
    margin-bottom:18px;
    border:none;
    outline:none;
    border-radius:8px;
    font-size:15px;
}

/* BUTTON */
.card button{
    width:100%;
    padding:14px;
    background:#007bff;
    border:none;
    border-radius:8px;
    color:#fff;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

.card button:hover{
    background:#0056b3;
    transform:translateY(-2px);
}

/* ERROR */
.error{
    margin-top:15px;
    color:#ffb3b3;
    font-size:14px;
}

/* RESPONSIVE */
@media(max-width:480px){
    .card{
        width:90%;
        padding:25px;
    }
}
</style>

</head>
<body>

<div class="card">
    <h2>🔐 Admin Login</h2>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button name="login">Login</button>
    </form>

    <div class="error"><?php echo $error; ?></div>
</div>

</body>
</html>
