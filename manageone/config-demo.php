<?php
// File: config-demo.php
// =================================================================
// Demo configuration using SQLite for local testing
// =================================================================

// Gemini API Key
define('GEMINI_API_KEY', 'AIzaSyC_JE5rLIUpz2lZLzmMFUh4cbRJJyrR9pM');

// --- ADMIN LOGIN CREDENTIALS ---
define('ADMIN_USER', 'admin@demo.com');
define('ADMIN_PASS', 'password123');

// --- EMAIL CONFIGURATION ---
define('FROM_EMAIL', 'no-reply@demo.com');

// --- DEMO DATABASE USING SQLITE ---
function get_db_connection() {
    $db_path = __DIR__ . '/demo_database.sqlite';
    
    try {
        $pdo = new PDO("sqlite:$db_path");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create tables if they don't exist
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS reviews (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                customer_name TEXT NOT NULL,
                customer_email TEXT NOT NULL,
                website_url TEXT NOT NULL,
                rating INTEGER NOT NULL,
                review_text TEXT,
                report_id TEXT,
                session_id TEXT,
                approved INTEGER DEFAULT 0,
                featured INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS evaluations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT,
                email TEXT,
                website TEXT,
                additional_info TEXT,
                score INTEGER,
                details TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        return null;
    }
}

// Convert MySQLi queries to PDO for demo purposes
function convertToPDO($pdo, $query, $params = []) {
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Query failed: " . $e->getMessage());
        return false;
    }
}

// Start session
session_start();
?>
