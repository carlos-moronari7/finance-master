<?php
session_start();
require_once '../classes/FinanceManager.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$fm = new FinanceManager($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action']) && $_POST['action'] === 'update') {
            $fm->updateTransaction($_POST['id'], $_POST);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Transaction updated!'];
        } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
            $fm->deleteTransaction($_POST['id']);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Transaction deleted!'];
        }
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
        <h1 class="page-title">Manage Transactions</h1>
        <?php include '../templates/messages.php'; ?>

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

        <!-- Transactions Table (Edit/Delete) -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Transaction Management</h5>
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
                                <th>Actions</th>
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
                                <td>
                                    <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $t['id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?php echo $t['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel">Edit Transaction</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                                                <div class="mb-3">
                                                    <label>Type</label>
                                                    <select name="type" class="form-select modern-input" required>
                                                        <option value="income" <?php echo $t['type'] === 'income' ? 'selected' : ''; ?>>Income</option>
                                                        <option value="expense" <?php echo $t['type'] === 'expense' ? 'selected' : ''; ?>>Expense</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Amount</label>
                                                    <input type="number" step="0.01" name="amount" class="form-control modern-input" value="<?php echo $t['amount']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Category</label>
                                                    <select name="category_id" class="form-select modern-input">
                                                        <option value="">No Category</option>
                                                        <?php foreach ($categories as $cat): ?>
                                                            <option value="<?php echo $cat['id']; ?>" <?php echo $t['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                                                <?php echo $cat['name']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Description</label>
                                                    <input type="text" name="description" class="form-control modern-input" value="<?php echo $t['description']; ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label>Date</label>
                                                    <input type="datetime-local" name="date" class="form-control modern-input" value="<?php echo date('Y-m-d\TH:i', strtotime($t['transaction_date'])); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Tags</label>
                                                    <input type="text" name="tags" class="form-control modern-input" value="<?php echo $t['tags']; ?>">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-neon">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../templates/footer.php'; ?>