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
        if (isset($_POST['action']) && $_POST['action'] === 'add') {
            $fm->addCategory($_POST);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Category added!'];
        } elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
            $fm->updateCategory($_POST['id'], $_POST);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Category updated!'];
        } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
            $fm->deleteCategory($_POST['id']);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Category deleted!'];
        }
    } catch (Exception $e) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error: ' . $e->getMessage()];
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$categories = $fm->getCategories();

include '../templates/header.php';
include '../templates/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <h1 class="page-title">Categories</h1>
        <?php include '../templates/messages.php'; ?>

        <!-- Add Category -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Add Category</h5>
                <form method="POST" class="row g-3">
                    <input type="hidden" name="action" value="add">
                    <div class="col-md-4">
                        <input type="text" name="name" class="form-control modern-input" placeholder="Category Name" required>
                    </div>
                    <div class="col-md-4">
                        <select name="type" class="form-select modern-input" required>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                            <option value="both">Both</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-neon w-100"><i class="fas fa-plus"></i> Add</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Category List</h5>
                <div class="table-responsive">
                    <table class="table modern-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                            <tr class="fade-in">
                                <td><?php echo $cat['name']; ?></td>
                                <td><?php echo ucfirst($cat['type']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $cat['id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($cat['user_id']): // Only allow deletion of user-created categories ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure? This will set related transactions to Uncategorized.');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?php echo $cat['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel">Edit Category</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                                <div class="mb-3">
                                                    <label>Name</label>
                                                    <input type="text" name="name" class="form-control modern-input" value="<?php echo $cat['name']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Type</label>
                                                    <select name="type" class="form-select modern-input" required>
                                                        <option value="income" <?php echo $cat['type'] === 'income' ? 'selected' : ''; ?>>Income</option>
                                                        <option value="expense" <?php echo $cat['type'] === 'expense' ? 'selected' : ''; ?>>Expense</option>
                                                        <option value="both" <?php echo $cat['type'] === 'both' ? 'selected' : ''; ?>>Both</option>
                                                    </select>
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