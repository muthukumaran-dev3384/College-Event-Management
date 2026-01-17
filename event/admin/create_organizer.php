<?php
include "../db.php";
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$msg = $error = "";

if (isset($_POST['create'])) {

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($name=="" || $email=="" || $phone=="" || $username=="" || $password=="") {
        $error = "All fields are required!";
    } else {

        // Duplicate check
        $check = $conn->query("SELECT id FROM organizer WHERE username='$username' OR email='$email'");
        if ($check->num_rows > 0) {
            $error = "Organizer already exists with same Username or Email!";
        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $conn->query("INSERT INTO organizer 
            (name, email, phone, username, password)
            VALUES 
            ('$name', '$email', '$phone', '$username', '$hashedPassword')");

            $msg = "✔ Organizer Created Successfully!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Organizer Login</title>
    <link rel="stylesheet" href="assets/css/admin.css">

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f2f4f7ff;
        }

        .card {
            width: 420px;
            background: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 15px;
            transition: 0.3s;
        }

        .form-group input:focus {
            border-color: #6f42c1;
            outline: none;
            box-shadow: 0 0 0 3px rgba(111,66,193,0.2);
        }

        .btn {
            width: 100%;
            padding: 13px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6f42c1, #59339d);
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.25);
        }

        .btn-secondary {
            background: #e9ecef;
            color: #333;
            margin-top: 10px;
        }

        .btn-secondary:hover {
            background: #ced4da;
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

        .hint {
            background: #f3f0ff;
            padding: 10px;
            border-radius: 8px;
            font-size: 13px;
            color: #444;
            margin-bottom: 15px;
            text-align: center;
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
    </style>
</head>

<body>

<div class="card">
    <h2>👨‍💼 Create Organizer Login</h2>

    <div class="hint">
        Organizer can create & manage events after login
    </div>

    <?php if ($error) echo "<div class='error'>$error</div>"; ?>
    <?php if ($msg) echo "<div class='success'>$msg</div>"; ?>

    <form method="post">
        <div class="form-group">
            <input type="text" name="name" placeholder="Organizer Name" required>
        </div>

        <div class="form-group">
            <input type="email" name="email" placeholder="Email Address" required>
        </div>

        <div class="form-group">
            <input type="text" name="phone" placeholder="Phone Number" required>
        </div>

        <div class="form-group">
            <input type="text" name="username" placeholder="Username" required>
        </div>

        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <button class="btn btn-primary" name="create">🚀 Create Organizer</button>
    </form>

   <a href="dashboard.php">⬅ Back to Dashboard</a>
</div>

</body>
</html>
