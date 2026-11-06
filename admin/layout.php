<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

$current_page = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
?>

<!-- HEADER + SIDEBAR CSS -->
<link rel="stylesheet" href="css/layout.css">

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


<!-- ================= HEADER ================= -->

<div class="header">

    <div class="left-header">
        <button id="hamburger" class="hamburger">☰</button>
        <h1>EXPIROCHAIN Admin Panel</h1>
    </div>

    <a href="../logout.php" class="logout-btn">Logout</a>

</div>


<!-- ================= SIDEBAR ================= -->

<div id="sidebar" class="sidebar">

    <div class="sidebar-header">
        <h3>Menu</h3>
        <button id="closeSidebar" class="close-btn">✖</button>
    </div>

    <a href="admin.php"
       class="<?php if($current_page=='admin.php') echo 'active'; ?>">
       Admin Home
    </a>

    <a href="admin_usr.php"
       class="<?php if($current_page=='admin_usr.php') echo 'active'; ?>">
       User Table
    </a>

    <a href="admin_vrf.php"
       class="<?php if($current_page=='admin_vrf.php') echo 'active'; ?>">
       User Verification
    </a>

</div>


<!-- ================= OVERLAY ================= -->

<div id="overlay" class="overlay"></div>


<!-- ================= SIDEBAR SCRIPT ================= -->

<script>

document.addEventListener("DOMContentLoaded", function(){

    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("overlay");
    const hamburger = document.getElementById("hamburger");
    const closeSidebar = document.getElementById("closeSidebar");

    /* OPEN SIDEBAR */

    if(hamburger){
        hamburger.addEventListener("click", function(){
            sidebar.classList.add("active");
            overlay.classList.add("active");
        });
    }

    /* CLOSE BUTTON */

    if(closeSidebar){
        closeSidebar.addEventListener("click", function(){
            sidebar.classList.remove("active");
            overlay.classList.remove("active");
        });
    }

    /* CLICK OUTSIDE SIDEBAR */

    if(overlay){
        overlay.addEventListener("click", function(){
            sidebar.classList.remove("active");
            overlay.classList.remove("active");
        });
    }

    /* ESC KEY CLOSE */

    document.addEventListener("keydown", function(e){
        if(e.key === "Escape"){
            sidebar.classList.remove("active");
            overlay.classList.remove("active");
        }
    });

});

</script>
