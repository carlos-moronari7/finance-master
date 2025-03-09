<?php
session_start();
require_once '../classes/FinanceManager.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$fm = new FinanceManager($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    try {
        $fm->addTransaction($_POST);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Transaction added!'];
    } catch (Exception $e) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error: ' . $e->getMessage()];
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$filters = array_filter($_GET);
$categories = $fm->getCategories();
$transactions = $fm->getTransactions($filters);

include '../templates/header.php';
include '../templates/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <h1 class="page-title">Transactions</h1>
        <?php include '../templates/messages.php'; ?>

        <!-- Add Transaction -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Add Transaction</h5>
                <form method="POST" class="row g-3">
                    <input type="hidden" name="action" value="add">
                    <div class="col-md-2">
                        <select name="type" class="form-select modern-input" required>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" name="amount" class="form-control modern-input" placeholder="Amount" required>
                    </div>
                    <div class="col-md-2">
                        <select name="category_id" class="form-select modern-input">
                            <option value="">No Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="description" class="form-control modern-input" placeholder="Description">
                    </div>
                    <div class="col-md-2">
                        <input type="datetime-local" name="date" class="form-control modern-input" required value="<?php echo date('Y-m-d\TH:i'); ?>">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-neon w-100"><i class="fas fa-plus"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Filter Transactions</h5>
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <input type="date" name="start_date" class="form-control modern-input" value="<?php echo $filters['start_date'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="end_date" class="form-control modern-input" value="<?php echo $filters['end_date'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="category_id" class="form-select modern-input">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($filters['category_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo $cat['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-neon w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transactions Table (View Only) -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Transaction History</h5>
                <div class="table-responsive">
                    <table class="table modern-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Tags</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $t): ?>
                            <tr class="fade-in">
                                <td><?php echo date('Y-m-d H:i', strtotime($t['transaction_date'])); ?></td>
                                <td class="<?php echo $t['type']; ?>"><?php echo ucfirst($t['type']); ?></td>
                                <td>$<?php echo number_format($t['amount'], 2); ?></td>
                                <td><?php echo $t['category_name'] ?? 'Uncategorized'; ?></td>
                                <td><?php echo $t['description'] ?? '-'; ?></td>
                                <td><?php echo $t['tags'] ?? '-'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../templates/footer.php'; ?>