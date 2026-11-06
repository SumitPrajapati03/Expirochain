<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$firm_name = isset($_SESSION['firm_name']) ? $_SESSION['firm_name'] : "User";

// Dynamic base URL detection
if (!defined('BASE_URL')) {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $parentDir = basename($scriptDir);
    if (in_array($parentDir, ['user', 'admin', 'api'])) {
        $scriptDir = dirname($scriptDir);
    }
    $scriptDir = str_replace('\\', '/', $scriptDir);
    define('BASE_URL', ($scriptDir === '/' || $scriptDir === '.' || $scriptDir === '') ? '' : rtrim($scriptDir, '/'));
}
$app_base = BASE_URL;
?>

<link rel="stylesheet" href="<?php echo $app_base; ?>/user/css/header.css" />
<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.2.0/css/line.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<div class="header">
  <div class="left-header">
    <button id="hamburger" class="hamburger">☰</button>
    <h1><a href="<?php echo $app_base; ?>/home.php">EXPIROCHAIN</a></h1>
  </div>

  <div class="right-header">
    Hello, <span class="firm-name"> <?php echo htmlspecialchars($firm_name); ?> </span>

    <a href="<?php echo $app_base; ?>/logout.php" class="logout-btn"> Logout </a>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("overlay");
    const hamburger = document.getElementById("hamburger");
    const closeSidebar = document.getElementById("closeSidebar");

    /* =========================
       OPEN SIDEBAR
    ========================= */
    if (hamburger) {
        hamburger.addEventListener("click", function () {
            sidebar.classList.add("active");
            overlay.classList.add("active");
        });
    }

    /* =========================
       CLOSE SIDEBAR (X BUTTON)
    ========================= */
    if (closeSidebar) {
        closeSidebar.addEventListener("click", function () {
            sidebar.classList.remove("active");
            overlay.classList.remove("active");
        });
    }

    /* =========================
       CLICK OUTSIDE (OVERLAY)
    ========================= */
    if (overlay) {
        overlay.addEventListener("click", function () {
            sidebar.classList.remove("active");
            overlay.classList.remove("active");
        });
    }

    /* =========================
       ESC KEY CLOSE
    ========================= */
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            sidebar.classList.remove("active");
            overlay.classList.remove("active");
        }
    });

});
</script>

<?php $current_page = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)); ?>

<!-- SIDEBAR -->

<div id="sidebar" class="sidebar">
  <div class="sidebar-header">
    <h3>Menu</h3>
    <button id="closeSidebar" class="close-btn">✖</button>
  </div>

  <a href="<?php echo $app_base; ?>/home.php" class="<?php if($current_page=='home.php') echo 'active'; ?>" >
    <i class="fa-solid fa-house"></i> Home
  </a>

  <a href="<?php echo $app_base; ?>/user/add_product.php" class="<?php if($current_page=='add_product.php') echo 'active'; ?>" >
    <i class="fa-solid fa-plus"></i> Add Product
  </a>

  <a href="<?php echo $app_base; ?>/user/products.php" class="<?php if($current_page=='products.php') echo 'active'; ?>" >
    <i class="fa-solid fa-database"></i> Products
  </a>

  <a href="<?php echo $app_base; ?>/user/add_stock.php" class="<?php if($current_page=='add_stock.php') echo 'active'; ?>" >
    <i class="uil uil-create-dashboard"></i> Add Stock
  </a>

  <a href="<?php echo $app_base; ?>/user/stock.php" class="<?php if($current_page=='stock.php') echo 'active'; ?>" >
   <i class="fa-solid fa-server"></i> Stock
  </a>

  <a href="<?php echo $app_base; ?>/user/expiry_tracker.php" class="<?php if($current_page=='expiry_tracker.php') echo 'active'; ?>" >
    <i class="fa-solid fa-triangle-exclamation"></i> Expiry Tracker
  </a>

  <a href="<?php echo $app_base; ?>/user/marketplace.php" class="<?php if($current_page=='marketplace.php') echo 'active'; ?>" >
    <i class="fa-solid fa-cart-shopping"></i> Marketplace
  </a>

  <a href="<?php echo $app_base; ?>/user/received_requests.php" class="<?php if($current_page=='received_requests.php') echo 'active'; ?>" >
    <i class="fa-solid fa-user-clock"></i> Requests
  </a>

  <a href="<?php echo $app_base; ?>/user/action_log.php" class="<?php if($current_page=='action_log.php') echo 'active'; ?>" >
    <i class="fa-solid fa-file-lines"></i> Action Log
  </a>

  <a href="<?php echo $app_base; ?>/user/account.php" class="<?php if($current_page=='account.php') echo 'active'; ?>" >
    <i class="fa-solid fa-circle-user"></i> Account
  </a>
</div>

<!-- OVERLAY -->

<div id="overlay" class="overlay"></div>

<!-- STYLE -->
 <style>
    /* CUSTOM RIGHT CLICK */
.custom-menu{
    position:fixed;
    display:none;
    padding: 10px;
    background:#0f172a;
    color: white;
    border:1px solid #212f4e;
    border-radius:8px;
    box-shadow:0 6px 15px rgba(0,0,0,0.2);
    z-index:9999;
    width:250px;
    overflow:hidden;
    transition: all 0.3s ease;
}

.custom-menu ul{
    list-style:none;
    margin:0;
    padding:0;
}

.custom-menu li{
    display: flex;
    gap: 20px;
    padding:10px 14px;
    cursor:pointer;
    margin: 5px 0;
    border-radius: 5px;
    font-size:18px;
    letter-spacing: 1px;
}

.custom-menu li i{
    font-weight: bold;
    vertical-align: left;
    text-align: left;
    align-items: left;
    justify-content: left;
}

.custom-menu li:nth-last-child(1){
    font-weight: bold;
    color: #ef4444;
}

.custom-menu li:nth-last-child(1):hover{
    background-color: #ef4444;
    color: #fff;
}

.custom-menu li:hover{
    background:#212f4e;
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

hr{
    color: #f4f6f9;
    opacity: 50%;
}
 </style>

<!-- RIGHT CLICK MENU -->

<div id="customMenu" class="custom-menu">
<ul>
<li onclick="goHome()"><i class="uil uil-estate"></i> Home</li>
<li onclick="refreshPage()"><i class="uil uil-sync"></i> Refresh</li>
<li onclick="productsPage()"><i class="uil uil-database"></i> Products</li>
<li onclick="stockPage()"><i class="uil uil-cloud-database-tree"></i> Stock</li>
<li onclick="expiryalertPage()"><i class="uil uil-exclamation-triangle"></i> Expiry Alert</li>
<li onclick="requestsPage()"><i class="uil uil-bell"></i> Requests</li>
<li onclick="goMarketplace()"><i class="uil uil-shopping-cart"></i> Marketplace</li>
<li onclick="goActionLog()"><i class="uil uil-file-alt"></i> Action Log</li>
<hr>
<li onclick="accountPage()"><i class="uil uil-user"></i> Account</li>
<li onclick="logout()"><i class="uil uil-signout"></i> Logout</li>
</ul>
</div>

<script>
document.addEventListener("DOMContentLoaded", function(){

const menu = document.getElementById("customMenu");

/* RIGHT CLICK */
document.addEventListener("contextmenu", function(e){

    e.preventDefault();

    /* TEMP SHOW FOR SIZE */
    menu.style.visibility = "hidden";
    menu.style.display = "block";

    const menuWidth = menu.offsetWidth;
    const menuHeight = menu.offsetHeight;

    let posX = e.clientX;
    let posY = e.clientY;

    if(posX + menuWidth > window.innerWidth){
        posX = window.innerWidth - menuWidth - 10;
    }

    if(posY + menuHeight > window.innerHeight){
        posY = window.innerHeight - menuHeight - 10;
    }

    menu.style.left = posX + "px";
    menu.style.top = posY + "px";

    menu.style.visibility = "visible";
});

/* HIDE */
document.addEventListener("click", function(){
    menu.style.display = "none";
});

/* ACTIONS */
const appBase = "<?php echo $app_base; ?>";
window.refreshPage = () => location.reload();
window.goHome = () => window.location.href = appBase + "/home.php";
window.goMarketplace = () => window.location.href = appBase + "/user/marketplace.php";
window.productsPage = () => window.location.href = appBase + "/user/products.php";
window.stockPage = () => window.location.href = appBase + "/user/stock.php";
window.expiryalertPage = () => window.location.href = appBase + "/user/expiry_tracker.php";
window.requestsPage = () => window.location.href = appBase + "/user/received_requests.php";
window.accountPage = () => window.location.href = appBase + "/user/account.php";
window.goActionLog = () => window.location.href = appBase + "/user/action_log.php";
window.logout = () => window.location.href = appBase + "/logout.php";

});
</script>
