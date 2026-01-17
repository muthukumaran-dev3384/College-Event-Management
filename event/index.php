<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>College Event Registration System</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:"Segoe UI", Arial, sans-serif;
}

body,html{
    height:100%;
    width:100%;
    transition:background 1s ease-in-out;
}

/* BACKGROUND */
body{
    position:relative;
    min-height:100vh;
    overflow:hidden;
}

/* OVERLAY (FIXED) */
.overlay{
    position:absolute;
    inset:0;
    background:rgba(0,0,0,0.5);
    z-index:1;
    pointer-events:none; /* ⭐ FIX */
}

/* NAVBAR */
nav{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    padding:12px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:rgba(0,0,0,0.7);
    z-index:10;
}

nav .logo{
    display:flex;
    align-items:center;
    gap:10px;
    color:#fff;
    font-weight:700;
    font-size:18px;
}

nav .logo img{
    height:42px;
    width:42px;
    border-radius:6px;
}

/* MENU */
nav .menu{
    display:flex;
    gap:15px;
}

nav .menu a{
    text-decoration:none;
    color:#fff;
    font-weight:600;
    padding:8px 18px;
    border-radius:6px;
    transition:.3s;
}

.admin{background:#007bff;}
.organizer{background:#ff9800;}
.student{background:#28a745;}

nav .menu a:hover{
    transform:translateY(-2px);
    opacity:.9;
}

/* MAIN CONTENT */
.container{
    position:relative;
    z-index:5;
    height:100vh;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    text-align:center;
    color:#fff;
    padding:20px;
}

.container h1{
    font-size:48px;
    margin-bottom:15px;
    text-shadow:2px 2px 10px rgba(0,0,0,.8);
}

.container p{
    font-size:18px;
    margin-bottom:30px;
    text-shadow:1px 1px 6px rgba(0,0,0,.7);
}

/* CENTER BUTTONS */
.center-buttons{
    display:flex;
    gap:20px;
    flex-wrap:wrap;
}

.center-buttons a{
    min-width:160px;
    text-align:center;
    padding:14px;
    font-weight:600;
    border-radius:8px;
    color:#fff;
    text-decoration:none;
    transition:.3s;
}

.center-buttons a:hover{
    transform:scale(1.05);
}

/* FOOTER */
footer{
    position:absolute;
    bottom:15px;
    width:100%;
    text-align:center;
    color:#ddd;
    font-size:14px;
    z-index:5;
}

/* RESPONSIVE */
@media(max-width:768px){
    nav{
        flex-direction:column;
        gap:10px;
    }
    nav .menu{
        flex-direction:column;
    }
    .container h1{font-size:34px;}
}
</style>
</head>

<body>

<div class="overlay"></div>

<!-- NAVBAR -->
<nav>
    <div class="logo">
        <img src=  "our college logo">
        <span>our College Name</span>
    </div>
    <div class="menu">
        <a href="admin/login.php" class="admin">Admin</a>
        <a href="organizer/login.php" class="organizer">Organizer</a>
        <a href="student/login.php" class="student">Student</a>
    </div>
</nav>

<!-- MAIN -->
<div class="container">
    <h1>College Event Registration System</h1>
    <p>Centralized platform for managing events, registrations & reports</p>

    
</div>

<footer>
    © <?php echo date("Y"); ?> College Event System
</footer>

<script>
const images=[
    'images/img1.jpg',
    'images/img2.jpg',
    'images/bg3.jpg'
];
let i=0;
function changeBg(){
    document.body.style.background=
    `linear-gradient(rgba(0,0,0,.5),rgba(0,0,0,.5)),
     url(${images[i]}) center/cover no-repeat`;
    i=(i+1)%images.length;
}
changeBg();
setInterval(changeBg,2000);
</script>

</body>
</html>
