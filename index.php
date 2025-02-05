<?php
session_start();
//var_dump($_SESSION);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Betting Site</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <header>
        <div class="header-left">
            <ul>
                <li><a href="index.php">Strona Główna</a></li>
                
            </ul>
        </div>

        <h1>Betting Site</h1>

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
        <section class="hero">
            <h2>Postaw zakład i wygraj!</h2>
            <p>Nowoczesna platforma do zakładów online.</p>
            <a href="dashboard.php" class="cta-button">Rozpocznij teraz</a>
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                <a href="admin.php" class="cta-button">admin panel</a>
            <?php endif; ?>
        </section>
    </main>
    <footer>
        <p>&copy; 2025 Betting Site. Wszelkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>
