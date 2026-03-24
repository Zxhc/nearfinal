<header>
    <nav class="nav-bar">
        <div class="logo">
            <a href="../../pages/dashBoard/dashBoard.php">
                <img src="../../src/hepc.jpg" alt="logo">
            </a>
        </div>
        
        <div class="hamburger" id="hamburger"> 
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>

       <div class="menu-links" id="menu-links">
    <ul>
        <li><a href="../../pages/dashBoard/dashBoard.php">
        <span class="material-symbols-outlined mobile-only">home</span>Dashboard</a></li>
        <hr class="mobile-only">
        
        <li><a href="../../pages/inventory/inventory.php">
        <span class="material-symbols-outlined mobile-only">inventory_2</span>Inventory</a></li>
        <hr class="mobile-only">
        
        <li><a href="../../pages/history/history.php">
        <span class="material-symbols-outlined mobile-only">history</span>History</a></li>
        <hr class="mobile-only">
        
       
        
        <li class="mobile-only"><a href="../../pages/PRS/prsStatus.php">
        <span class="material-symbols-outlined mobile-only">list_alt_check</span>View PRS Status</a></li>
        <hr class="mobile-only">
        
        <li class="mobile-only"><a href="../../component/settings/logout.php">
        <span class="material-symbols-rounded mobile-only">logout</span>Logout</a></li>
    </ul>
</div>
    </nav>
</header>

<script>
const hamburger = document.getElementById('hamburger');
const menuLinks = document.getElementById('menu-links');

hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('active');
    menuLinks.classList.toggle('active');
});
</script>