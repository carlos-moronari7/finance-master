<?php
session_start();
require_once '../classes/FinanceManager.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $user_id = FinanceManager::login($username, $password);
    if ($user_id) {
        $_SESSION['user_id'] = $user_id;
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Invalid username or password'];
    }
}

include '../templates/header.php';
?>

<main class="main-content" style="margin-left: 0; display: flex; justify-content: center; align-items: center; height: 100vh;">
    <div class="card" style="width: 100%; max-width: 400px;">
        <div class="card-body">
            <h1 class="page-title text-center">Login</h1>
            <?php include '../templates/messages.php'; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control modern-input" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control modern-input" required>
                </div>
                <button type="submit" class="btn btn-neon w-100">Login</button>
                <p class="text-center mt-3">Don't have an account? <a href="signup.php">Sign Up</a></p>
            </form>
        </div>
    </div>
</main>

<?php include '../templates/footer.php'; ?>