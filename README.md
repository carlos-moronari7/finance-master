## What It Is
Unleash your financial potential with **Finance Master**—the ultimate personal finance management web app that puts you in control! Crafted with precision using PHP, this powerhouse delivers a stunningly modern interface, rock-solid security, and jaw-dropping features. Track your income and expenses with ease, visualize your net worth with dynamic charts, customize categories to fit your life, and switch between a sleek dark mode and a crisp light mode with a single click. Whether you’re a budgeting newbie or a money maestro, Finance Master is your ticket to financial clarity and confidence. Built for professionals, loved by all—get ready to master your money like never before!

## How to Run
Ready to take charge of your finances? Let’s get Finance Master up and running in minutes with these easy steps:

1. **Set Up Your Database Powerhouse**
   - Fire up MySQL and create a database (e.g., `finance_master_db`).
   - Grab the schema from `database/finance_master.sql` (create it if missing) and import it:
     ```sql
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


2. **Edit config/db_connect.php with your MySQL creds:**
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

3.**Kickstart Your Server**
4.Launch your web server with Laragon, XAMPP, or your preferred setup (PHP and MySQL required).
5.Point it to the finance-master/public directory and let the magic ignite!

Dive In
Fire up your browser and head to http://localhost/finance-master/public/login.php.
Log in with your existing credentials or sign up in seconds with a new username and password to unlock your financial dashboard.

Explore the Power
Jump to index.php for a real-time financial overview that’ll blow your mind.

Manage transactions like a pro on transactions.php.

Unlock deep insights with analytics.php (if available) and watch your spending patterns come to life.

Flip the theme switch in the navbar to toggle between dark and light modes—because style matters!

Don’t wait—start mastering your money today and elevate your financial game with Finance Master!
