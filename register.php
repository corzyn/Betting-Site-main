<?php
require 'db.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, balance, isAdmin) VALUES (?, ?, ?, 1000, 0)");
    $stmt->execute([$username, $email, $password]);

    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <header>
        <div class="header-left">
            <a href="index.php">Strona Główna</a>
            <a href="bet.php">Zakłady</a>
        </div>
        <div class="header-title">
            <h1>Betting Site</h1>
        </div>
        <div class="header-right">
            <a href="login.php">Logowanie</a>
            <a href="register.php">Rejestracja</a>
        </div>
    </header>
    <main>

        <form method="POST">
        <h2>Rejestracja</h2>
        <hr>
            <label for="username">Nazwa użytkownika</label>
            <input type="text" id="username" name="username" required>
            
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Hasło</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Zarejestruj się</button>
            <button type="button" onclick="window.location.href='login.php';">Masz już konto? Zaloguj się</button>
        </form>
    </main>
    <footer>
        <p>&copy; 2025 Betting Site. Wszelkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>
