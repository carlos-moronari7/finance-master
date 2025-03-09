<?php
require_once '../config/db_connect.php';

class FinanceManager {
    private $pdo;
    private $user_id;

    public function __construct($user_id) {
        $this->pdo = Database::getInstance();
        $this->user_id = $user_id;
    }

    public function getBalance() {
        $stmt = $this->pdo->prepare("SELECT SUM(CASE WHEN type='income' THEN amount ELSE -amount END) as total 
            FROM transactions WHERE user_id = ?");
        $stmt->execute([$this->user_id]);
        return $stmt->fetch()['total'] ?? 0;
    }

    public function getIncomeExpenseTotals() {
        $stmt = $this->pdo->prepare("
            SELECT 
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
            FROM transactions 
            WHERE user_id = ?");
        $stmt->execute([$this->user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            error_log("No results for user_id: " . $this->user_id);
            return ['total_income' => 0, 'total_expense' => 0];
        }
        error_log("Totals for user_id " . $this->user_id . ": income=" . ($result['total_income'] ?? 'null') . ", expense=" . ($result['total_expense'] ?? 'null'));
        return [
            'total_income' => $result['total_income'] ?? 0,
            'total_expense' => $result['total_expense'] ?? 0
        ];
    }

    public function getCategories() {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE user_id = ? OR user_id IS NULL");
        $stmt->execute([$this->user_id]);
        return $stmt->fetchAll();
    }

    public function addCategory($data) {
        $stmt = $this->pdo->prepare("INSERT INTO categories (name, type, user_id) VALUES (?, ?, ?)");
        return $stmt->execute([$data['name'], $data['type'], $this->user_id]);
    }

    public function updateCategory($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE categories SET name = ?, type = ? WHERE id = ? AND user_id = ?");
        return $stmt->execute([$data['name'], $data['type'], $id, $this->user_id]);
    }

    public function deleteCategory($id) {
        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $this->user_id]);
    }

    public function getTransactions($filters = []) {
        $where = ["t.user_id = ?"];
        $params = [$this->user_id];
        
        if (!empty($filters['start_date'])) {
            $where[] = "t.transaction_date >= ?";
            $params[] = $filters['start_date']; // Fixed typo here
        }
        if (!empty($filters['end_date'])) {
            $where[] = "t.transaction_date <= ?";
            $params[] = $filters['end_date']; // Fixed typo here
        }
        if (!empty($filters['category_id'])) {
            $where[] = "t.category_id = ?";
            $params[] = $filters['category_id'];
        }

        $sql = "SELECT t.*, c.name as category_name 
                FROM transactions t 
                LEFT JOIN categories c ON t.category_id = c.id 
                WHERE " . implode(" AND ", $where) . " 
                ORDER BY t.transaction_date DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function addTransaction($data) {
        $stmt = $this->pdo->prepare("INSERT INTO transactions (user_id, type, amount, category_id, description, transaction_date, tags) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $this->user_id,
            $data['type'],
            $data['amount'],
            !empty($data['category_id']) ? $data['category_id'] : null,
            $data['description'] ?? null,
            $data['date'],
            $data['tags'] ?? null
        ]);
    }

    public function updateTransaction($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE transactions 
            SET type = ?, amount = ?, category_id = ?, description = ?, transaction_date = ?, tags = ? 
            WHERE id = ? AND user_id = ?");
        return $stmt->execute([
            $data['type'],
            $data['amount'],
            !empty($data['category_id']) ? $data['category_id'] : null,
            $data['description'] ?? null,
            $data['date'],
            $data['tags'] ?? null,
            $id,
            $this->user_id
        ]);
    }

    public function deleteTransaction($id) {
        $stmt = $this->pdo->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $this->user_id]);
    }

    public function getAnalyticsData() {
        $expense_data = $this->pdo->prepare("SELECT c.name, SUM(t.amount) as total 
            FROM transactions t 
            LEFT JOIN categories c ON t.category_id = c.id 
            WHERE t.type = 'expense' AND t.user_id = ? 
            GROUP BY t.category_id, c.name");
        $expense_data->execute([$this->user_id]);

        $monthly_data = $this->pdo->prepare("SELECT DATE_FORMAT(t.transaction_date, '%Y-%m') as month, 
            SUM(CASE WHEN t.type='income' THEN t.amount ELSE 0 END) as income,
            SUM(CASE WHEN t.type='expense' THEN t.amount ELSE 0 END) as expense
            FROM transactions t
            WHERE t.user_id = ? 
            GROUP BY month 
            ORDER BY month");
        $monthly_data->execute([$this->user_id]);

        return [
            'expenses' => $expense_data->fetchAll(),
            'monthly' => $monthly_data->fetchAll()
        ];
    }

    public static function login($username, $password) {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            return $user['id'];
        }
        return false;
    }

    public static function signup($username, $password) {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            return false; // Username taken
        }
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        return $stmt->execute([$username, $hashed_password]);
    }
}
?>