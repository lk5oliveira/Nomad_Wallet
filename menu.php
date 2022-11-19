
<div class="" id="menu-bar">
            <a href="javascript:void(0);" class="hamburger-icon" onclick="slideIn()">
                <i class="fa fa-bars"></i>
            </a>
            <!--Profile picture and name-->
            <div id="profile">
                <a href="profile.php" id="picture">
                    <i id="pic" class="fa-solid fa-user"></i>
                </a>
                <div class="name-status-close" id="name-status">
                    <h6><?= $_SESSION['user']?></h6>
                    <a href="accounts.php" id="mode">
    
                    </a>
                </div>
            </div>
            <!--Menu options-->
            <div id="menu">
                <div id="container-options">
                    <div id="menu-option">
                        <a href="panel.php" id="option">
                            <i id="option-icon" class="menu-icon fa-solid fa-gauge-high"></i>
                            <p class="menu-text" id="menu-text">Dashboard</p>
                            <?php if ($_SERVER['REQUEST_URI'] == "/panel.php") : ?>
                            <?= '<i class="fa-solid fa-circle-dot" id="dot"></i>' ?>
                            <?php endif;?>
                        </a>
                    </div>
                    <div id="menu-option">
                        <a href="history.php" id="option">
                        <i id="option-icon" class="fa-solid fa-table-list"></i>
                            <p class="menu-text" id="menu-text">Transactions</p>
                            <?php if ($_SERVER['REQUEST_URI'] == "/history.php") : ?>
                            <?= '<i class="fa-solid fa-circle-dot" id="dot"></i>' ?>
                            <?php endif;?>
                        </a>
                    </div>
                    <div id="menu-option">
                        <a href="accounts.php" id="option">
                        <i id="option-icon" class="fa-solid fa-building-columns"></i>
                            <p class="menu-text" id="menu-text">Wallets</p>
                            <?php if ($_SERVER['REQUEST_URI'] == "/accounts.php") : ?>
                            <?= '<i class="fa-solid fa-circle-dot" id="dot"></i>' ?>
                            <?php endif;?>
                        </a>
                    </div>
                </div>
            </div>
            <!--Exit button-->
            <div id="exit-container">
                <div id="option">
                    <a href="include/login/signout.php" id="option">
                        <i id="option-icon" class="fa-solid fa-right-from-bracket"></i> 
                        <p class="menu-text" id="menu-text">Exit</p>
                    </a>
                </div>
            </div>
        </div>