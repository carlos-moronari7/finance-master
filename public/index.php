<?php
session_start();
require_once '../classes/FinanceManager.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$fm = new FinanceManager($_SESSION['user_id']);
$balance = $fm->getBalance();

// Get total income and expenses
$totals = $fm->getIncomeExpenseTotals();
$total_income = $totals['total_income'];
$total_expense = $totals['total_expense'];

// Debug output
error_log("Index.php - user_id: " . $_SESSION['user_id'] . ", total_income: " . $total_income . ", total_expense: " . $total_expense);

include '../templates/header.php';
include '../templates/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <h1 class="page-title">Dashboard</h1>
        <?php include '../templates/messages.php'; ?>

        <!-- Net Total (Centered at Top) -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="card-title">Net Total</h3>
                        <h1 class="display-4 fw-bold <?php echo $balance >= 0 ? 'income' : 'expense'; ?>">
                            $<?php echo number_format($balance, 2); ?>
                        </h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Income and Expenses Charts Side by Side -->
        <div class="row">
            <!-- Income Chart -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Income</h5>
                        <canvas id="incomeChart" style="max-height: 200px;"></canvas>
                        <?php if ($total_income == 0): ?>
                            <p class="text-center mt-3 text-muted">No income transactions yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Expense Chart -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Expenses</h5>
                        <canvas id="expenseChart" style="max-height: 200px;"></canvas>
                        <?php if ($total_expense == 0): ?>
                            <p class="text-center mt-3 text-muted">No expense transactions yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../templates/footer.php'; ?>

<!-- Chart.js and Chart Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, starting chart setup');

        // Income Chart
        const incomeCtx = document.getElementById('incomeChart');
        if (incomeCtx) {
            console.log('Income canvas found, data:', <?php echo $total_income; ?>);
            new Chart(incomeCtx, {
                type: 'bar',
                data: {
                    labels: ['Income'],
                    datasets: [{
                        label: 'Total Income',
                        data: [<?php echo $total_income; ?>],
                        backgroundColor: '#34c759',
                        borderColor: '#34c759',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true, ticks: { color: '<?php echo isset($_SESSION['theme']) && $_SESSION['theme'] === 'light' ? '#2c2e38' : '#d1d4dc'; ?>' } },
                        x: { ticks: { color: '<?php echo isset($_SESSION['theme']) && $_SESSION['theme'] === 'light' ? '#2c2e38' : '#d1d4dc'; ?>' } }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Total Income: $' + context.raw.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                }
                            }
                        }
                    }
                }
            });
        } else {
            console.error('Income chart canvas not found!');
        }

        // Expense Chart
        const expenseCtx = document.getElementById('expenseChart');
        if (expenseCtx) {
            console.log('Expense canvas found, data:', <?php echo $total_expense; ?>);
            new Chart(expenseCtx, {
                type: 'bar',
                data: {
                    labels: ['Expenses'],
                    datasets: [{
                        label: 'Total Expenses',
                        data: [<?php echo $total_expense; ?>],
                        backgroundColor: '#ff3b30',
                        borderColor: '#ff3b30',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true, ticks: { color: '<?php echo isset($_SESSION['theme']) && $_SESSION['theme'] === 'light' ? '#2c2e38' : '#d1d4dc'; ?>' } },
                        x: { ticks: { color: '<?php echo isset($_SESSION['theme']) && $_SESSION['theme'] === 'light' ? '#2c2e38' : '#d1d4dc'; ?>' } }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Total Expenses: $' + context.raw.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                }
                            }
                        }
                    }
                }
            });
        } else {
            console.error('Expense chart canvas not found!');
        }
    });
</script>