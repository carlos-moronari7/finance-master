<?php
session_start();
require_once '../classes/FinanceManager.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$fm = new FinanceManager($_SESSION['user_id']);
$analytics = $fm->getAnalyticsData();

include '../templates/header.php';
include '../templates/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <h1 class="page-title">Analytics</h1>
        <?php include '../templates/messages.php'; ?>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Expenses by Category</h5>
                        <canvas id="expenseChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Monthly Overview</h5>
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../templates/footer.php'; ?>

<script>
    const expenseCtx = document.getElementById('expenseChart').getContext('2d');
    new Chart(expenseCtx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_column($analytics['expenses'], 'name')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($analytics['expenses'], 'total')); ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
            }]
        },
        options: { 
            responsive: true, 
            plugins: { 
                legend: { position: 'top', labels: { color: '<?php echo isset($_SESSION['theme']) && $_SESSION['theme'] === 'light' ? '#2c2e38' : '#d1d4dc'; ?>' } } 
            } 
        }
    });

    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($analytics['monthly'], 'month')); ?>,
            datasets: [
                { label: 'Income', data: <?php echo json_encode(array_column($analytics['monthly'], 'income')); ?>, backgroundColor: '#34c759' },
                { label: 'Expenses', data: <?php echo json_encode(array_column($analytics['monthly'], 'expense')); ?>, backgroundColor: '#ff3b30' }
            ]
        },
        options: { 
            responsive: true, 
            scales: { 
                y: { beginAtZero: true, ticks: { color: '<?php echo isset($_SESSION['theme']) && $_SESSION['theme'] === 'light' ? '#2c2e38' : '#d1d4dc'; ?>' } }, 
                x: { ticks: { color: '<?php echo isset($_SESSION['theme']) && $_SESSION['theme'] === 'light' ? '#2c2e38' : '#d1d4dc'; ?>' } }
            },
            plugins: { 
                legend: { labels: { color: '<?php echo isset($_SESSION['theme']) && $_SESSION['theme'] === 'light' ? '#2c2e38' : '#d1d4dc'; ?>' } } 
            }
        }
    });
</script>