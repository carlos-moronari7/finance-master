How to Run Finance Master

Get started with Finance Master and take control of your finances by following these steps:

1. Set Up the Database

Start MySQL and create a new database (e.g., finance_master_db).

Import the Schema: If the file database/finance_master.sql exists, import it. Otherwise, create the necessary tables using:

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    category_id INT,
    description TEXT,
    transaction_date DATE NOT NULL,
    tags VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

2. Configure Database Connection

Edit config/db_connect.php and replace the placeholders with your MySQL credentials:

<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $this->pdo = new PDO("mysql:host=localhost;dbname=finance_master_db;charset=utf8mb4", "your_username", "your_password");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
}
?>

3. Start the Server

Use XAMPP, Laragon, or any other local web server that supports PHP and MySQL.

Ensure the web server is pointing to the finance-master/public directory.

4. Access the Application

Open your browser and go to http://localhost/finance-master/public/login.php.

Log in with an existing account or create a new one.

5. Explore the Features

Dashboard: View a real-time summary of your finances (index.php).

Transactions: Add, edit, and manage transactions (transactions.php).

Analytics: Get insights into your financial trends (analytics.php, if available).

Theme Switcher: Toggle between light and dark mode in the navbar.

Start using Finance Master today to take full control of your financial management!


**FUTURE**
xlxs export
