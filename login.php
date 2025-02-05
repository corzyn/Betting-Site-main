<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['isAdmin'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Nieprawidłowe dane logowania!";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<header>
    <div class="header-left">
        <ul>
            <li><a href="index.php">Strona Główna</a></li>
        </ul>
    </div>

    <div class="header-title">
        <h1>Betting Site</h1>
    </div>

    <div class="header-right">
        <ul>
        
            <?php if(isset($_SESSION['user_id'])): ?>
                <li><a href="dashboard.php">Panel</a></li>
                <li><a href="logout.php">Wyloguj</a></li>
            <?php else: ?>
                <li><a href="login.php">Logowanie</a></li>
                <li><a href="register.php">Rejestracja</a></li>
            <?php endif; ?>
        </ul>
    </div>
</header>
    <main>
        <form method="POST">
            <h2>Logowanie</h2>
            <hr>
            <label for="username">Nazwa użytkownika</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Hasło</label>
            <input type="password" id="password" name="password" required>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <button type="submit">Zaloguj</button>
            <button type="button" onclick="window.location.href='register.php';">Rejestracja</button>
        </form>
    </main>
    <footer>
        <p>&copy; 2025 Betting Site. Wszelkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>
