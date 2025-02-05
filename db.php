<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'betting_system';

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");


    $pdo->exec("USE $dbname");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            balance DECIMAL(10, 2) DEFAULT 1000.00,
            isAdmin BOOLEAN DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS matches (
            id INT AUTO_INCREMENT PRIMARY KEY,
            team_a VARCHAR(100) NOT NULL,
            team_b VARCHAR(100) NOT NULL,
            start_time DATETIME NOT NULL,
            odds_team_a DECIMAL(5, 2) NOT NULL,
            odds_draw DECIMAL(5, 2) NOT NULL,
            odds_team_b DECIMAL(5, 2) NOT NULL
        )
    ");
    

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            match_id INT NOT NULL,
            bet_type ENUM('team_a', 'draw', 'team_b') NOT NULL,
            stake DECIMAL(10, 2) NOT NULL,
            potential_win DECIMAL(10, 2) DEFAULT 0.00,
            status ENUM('wygrany', 'przegrany', 'oczekujący') DEFAULT 'oczekujący',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE
        )
    ");
    

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS results (
            match_id INT PRIMARY KEY,
            score_team_a INT NOT NULL,
            score_team_b INT NOT NULL,
            FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE
        )
    ");
    

} catch (PDOException $e) {
    die("Błąd połączenia lub tworzenia bazy danych: " . $e->getMessage());
}
?>
