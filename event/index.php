<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>College Event Registration System</title>

<style>
/* ================= GLOBAL RESET ================= */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:"Poppins","Segoe UI",Arial,sans-serif;
}

/* ================= BODY ================= */
body{
    min-height:100vh;
    background:
        radial-gradient(circle at top left,#3a1c71,transparent 40%),
        radial-gradient(circle at bottom right,#d76d77,transparent 40%),
        linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    color:#fff;
    overflow-x:hidden;
}

/* ================= NAVBAR ================= */
nav{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    padding:14px 40px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:rgba(0,0,0,0.55);
    backdrop-filter:blur(12px);
    box-shadow:0 8px 30px rgba(0,0,0,0.4);
    z-index:100;
}

nav .logo{
    display:flex;
    align-items:center;
    gap:12px;
    font-weight:700;
    font-size:16px;
}

nav .logo img{
    height:44px;
    width:44px;
    border-radius:10px;
}

nav .menu{
    display:flex;
    gap:16px;
}

nav .menu a{
    text-decoration:none;
    color:#fff;
    font-weight:600;
    padding:10px 22px;
    border-radius:30px;
    transition:.35s ease;
    letter-spacing:.3px;
}

.admin{background:linear-gradient(135deg,#0d6efd,#5a8cff);}
.organizer{background:linear-gradient(135deg,#fd7e14,#ff9f43);}
.student{background:linear-gradient(135deg,#198754,#28c76f);}

nav .menu a:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 25px rgba(0,0,0,.35);
}

/* ================= MAIN ================= */
.container{
    padding-top:130px;
    display:flex;
    flex-direction:column;
    align-items:center;
}

/* ================= HERO SLIDER ================= */
.hero{
    position:relative;
    width:92%;
    max-width:1400px;
    height:620px;
    border-radius:28px;
    overflow:hidden;
    background:#000;
    box-shadow:
        0 30px 70px rgba(0,0,0,.6),
        inset 0 0 0 1px rgba(255,255,255,.08);
}

.hero img{
    width:100%;
    height:100%;
    object-fit:cover;
    transition:opacity .7s ease, transform 6s linear;
}

.hero::after{
    content:"";
    position:absolute;
    inset:0;
    background:
        linear-gradient(to bottom,rgba(0,0,0,.2),rgba(0,0,0,.85));
}

.hero-content{
    position:absolute;
    inset:0;
    z-index:2;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    text-align:center;
    padding:40px;
    animation:fadeUp 1.2s ease;
}

.hero-content h1{
    font-size:48px;
    font-weight:800;
    margin-bottom:14px;
    letter-spacing:.5px;
}

.hero-content p{
    font-size:19px;
    max-width:620px;
    opacity:.95;
}

/* ================= SLIDER BUTTONS ================= */
.slider-btn{
    position:absolute;
    top:50%;
    transform:translateY(-50%);
    background:rgba(255,255,255,0.15);
    backdrop-filter:blur(10px);
    border:none;
    color:#fff;
    font-size:32px;
    width:54px;
    height:54px;
    border-radius:50%;
    cursor:pointer;
    z-index:5;
    transition:.3s;
}

.slider-btn:hover{
    background:#0d6efd;
    box-shadow:0 12px 30px rgba(13,110,253,.6);
}

.prev{left:20px;}
.next{right:20px;}

/* ================= CONDUCTED EVENTS ================= */
.gallery-section{
    margin-top:70px;
    width:90%;
    max-width:1200px;
}

.gallery-section h2{
    text-align:center;
    margin-bottom:35px;
    font-size:30px;
    font-weight:700;
}

/* ================= EVENT GRID ================= */
.event-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:28px;
}

.event-card{
    position:relative;
    height:190px;
    border-radius:20px;
    overflow:hidden;
    cursor:pointer;
    background:#000;
    box-shadow:
        0 20px 45px rgba(0,0,0,.55),
        inset 0 0 0 1px rgba(255,255,255,.08);
    transition:.4s ease;
}

.event-card img{
    width:100%;
    height:100%;
    object-fit:cover;
    transition:transform .6s ease;
}

.event-card::after{
    content:"";
    position:absolute;
    inset:0;
    background:linear-gradient(to top,rgba(0,0,0,.75),transparent);
    opacity:0;
    transition:.4s;
}

.event-card:hover{
    transform:translateY(-8px) scale(1.02);
}

.event-card:hover img{
    transform:scale(1.15);
}

.event-card:hover::after{
    opacity:1;
}

.event-text{
    position:absolute;
    bottom:18px;
    left:18px;
    font-size:18px;
    font-weight:700;
    z-index:2;
}

/* ================= STATS ================= */
.stats{
    margin:80px 0 60px;
    width:90%;
    max-width:1100px;
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:28px;
}

.stat-box{
    background:rgba(255,255,255,.15);
    backdrop-filter:blur(14px);
    padding:28px;
    border-radius:22px;
    text-align:center;
    box-shadow:0 18px 40px rgba(0,0,0,.45);
    transition:.35s;
}

.stat-box:hover{
    transform:translateY(-6px);
    box-shadow:0 25px 60px rgba(0,0,0,.6);
}

.stat-box h3{
    font-size:36px;
    margin-bottom:6px;
}

.stat-box span{
    opacity:.9;
    letter-spacing:.4px;
}

/* ================= FOOTER ================= */
footer{
    margin-bottom:30px;
    font-size:14px;
    opacity:.75;
}

/* ================= ANIMATIONS ================= */
@keyframes fadeUp{
    from{opacity:0; transform:translateY(30px);}
    to{opacity:1; transform:translateY(0);}
}

/* ================= RESPONSIVE ================= */
@media(max-width:768px){
    .hero{height:460px;}
    .hero-content h1{font-size:34px;}
    nav{flex-direction:column; gap:10px;}
}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

<!-- ================= NAVBAR ================= -->
<nav>
    <div class="logo">
        <img src="images/logo1.jpg">
        <span>Your  College Name</span>
    </div>
   <div class="menu">
    <a href="admin/login.php" class="admin">
        <i class="fa-solid fa-user-shield"></i> Admin
    </a>

    <a href="organizer/login.php" class="organizer">
        <i class="fa-solid fa-user-tie"></i> Organizer
    </a>

    <a href="student/login.php" class="student">
        <i class="fa-solid fa-user-graduate"></i> Student
    </a>
</div>

</nav>

<!-- ================= MAIN ================= -->
<section class="container">

    <!-- HERO SLIDER -->
    <div class="hero">
        <img id="heroImg" src="your college logo">

        <div class="hero-content">
            <h1>College Event Registration System</h1>
            <p>Premium centralized platform for managing events, registrations, organizers and analytical reports.</p>
        </div>

        <button class="slider-btn prev" onclick="prevSlide()">❮</button>
        <button class="slider-btn next" onclick="nextSlide()">❯</button>
    </div>

    <!-- CONDUCTED EVENTS -->
    <div class="gallery-section">
        <h2>🎉 Conducted Events</h2>
        <div class="event-grid">
            <div class="event-card"><img src="images/tech.jpg"><div class="event-text">Tech Symposium</div></div>
            <div class="event-card"><img src="images/clutural.jpg"><div class="event-text">Cultural Fest</div></div>
            <div class="event-card"><img src="images/pongal.jpg"><div class="event-text">Pongal Celebration</div></div>
            <div class="event-card"><img src="images/volly.jpg"><div class="event-text">Sports Meet</div></div>
        </div>
    </div>

    <!-- STATS -->
    <div class="stats">
        <div class="stat-box"><h3>25+</h3><span>Total Events</span></div>
        <div class="stat-box"><h3>1200+</h3><span>Student Registrations</span></div>
        <div class="stat-box"><h3>15</h3><span>Active Organizers</span></div>
    </div>

    <footer>
        © <?php echo date("Y"); ?> College Event System
    </footer>
</section>

<script>
const slides=[
    "images/img1.jpg",
    "images/img2.jpg",
    "images/img3.jpg"
];

let index=0;
const heroImg=document.getElementById("heroImg");

function showSlide(){
    heroImg.style.opacity=0;
    setTimeout(()=>{
        heroImg.src=slides[index];
        heroImg.style.opacity=1;
        heroImg.style.transform="scale(1.05)";
    },350);
}

function nextSlide(){
    index=(index+1)%slides.length;
    showSlide();
}

function prevSlide(){
    index=(index-1+slides.length)%slides.length;
    showSlide();
}

setInterval(nextSlide,6000);
</script>

</body>
</html>
