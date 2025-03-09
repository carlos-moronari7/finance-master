<?php if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
} ?>
<nav class="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-wallet logo"></i>
        <span class="sidebar-title">Finance Master</span>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>" title="Dashboard">
                <i class="fas fa-home"></i>
                <span class="nav-text">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="transactions.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'transactions.php' ? 'active' : ''; ?>" title="Transactions">
                <i class="fas fa-exchange-alt"></i>
                <span class="nav-text">Transactions</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="manage_transactions.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'manage_transactions.php' ? 'active' : ''; ?>" title="Manage Transactions">
                <i class="fas fa-tools"></i>
                <span class="nav-text">Manage Transactions</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="categories.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'categories.php' ? 'active' : ''; ?>" title="Categories">
                <i class="fas fa-list"></i>
                <span class="nav-text">Categories</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="analytics.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'analytics.php' ? 'active' : ''; ?>" title="Analytics">
                <i class="fas fa-chart-line"></i>
                <span class="nav-text">Analytics</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="logout.php" class="nav-link" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
                <span class="nav-text">Logout</span>
            </a>
        </li>
    </ul>
    <div class="sidebar-footer">
        <button class="btn theme-toggle" id="themeToggle" title="Toggle Theme">
            <i class="fas fa-adjust"></i>
        </button>
    </div>
</nav>