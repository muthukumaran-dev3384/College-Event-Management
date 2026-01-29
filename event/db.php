<?php
/* ===============================
   Database Configuration
   =============================== */

$host = "localhost";
$user = "root";
$pass = "";
$db   = "event_system";

/* ===============================
   Database Connection
   =============================== */

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

/* ===============================
   Session & Timezone
   =============================== */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set("Asia/Kolkata");

/* ===============================
   Security (Optional)
   =============================== */
// mysqli_set_charset($conn, "utf8");

?>
