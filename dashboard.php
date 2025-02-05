<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$balance = $user['balance'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_bet'])) {
    $match_id = $_POST['match_id'];
    $bet_type = $_POST['bet_type'];
    $stake = $_POST['stake'];

    if ($stake <= $balance) {
        $new_balance = $balance - $stake;
        $stmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
        $stmt->execute([$new_balance, $user_id]);

        $stmt = $pdo->prepare("SELECT * FROM matches WHERE id = ?");
        $stmt->execute([$match_id]);
        $match = $stmt->fetch();

        $odds = 0;
        if ($bet_type == 'team_a') {
            $odds = $match['odds_team_a'];
        } elseif ($bet_type == 'draw') {
            $odds = $match['odds_draw'];
        } elseif ($bet_type == 'team_b') {
            $odds = $match['odds_team_b'];
        }

        $potential_win = $stake * $odds;

        $stmt = $pdo->prepare("INSERT INTO bets (user_id, match_id, bet_type, stake, potential_win, status) VALUES (?, ?, ?, ?, ?, 'oczekujący')");
        $stmt->execute([$user_id, $match_id, $bet_type, $stake, $potential_win]);

        $success = "Zakład został przyjęty!";
    } else {
        $error = "Masz za mało żetonów na ten zakład.";
    }
}

$stmt = $pdo->query("SELECT * FROM matches");
$matches = $stmt->fetchAll();
$stmt = $pdo->prepare("
    SELECT bets.*, matches.team_a, matches.team_b 
    FROM bets 
    JOIN matches ON bets.match_id = matches.id 
    WHERE bets.user_id = ? AND bets.status = 'oczekujący'
");
$stmt->execute([$user_id]);
$bets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard i Zakłady</title>
    <link rel="stylesheet" href="dashboard.css">
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php">Wyloguj</a></li>
            <?php else: ?>
                <li><a href="login.php">Logowanie</a></li>
                <li><a href="register.php">Rejestracja</a></li>
            <?php endif; ?>
        </ul>
    </div>
</header>


    <main class="main-content">
        <section>
            <h2>Obstawianie Zakładu</h2>

            <?php if (!empty($success)) echo "<p>$success</p>"; ?>
            <?php if (!empty($error)) echo "<p>$error</p>"; ?>

            <form method="POST">
                <label>Wybierz mecz:
                    <select name="match_id" required>
                        <?php foreach ($matches as $match): ?>
                            <option value="<?php echo $match['id']; ?>"><?php echo $match['team_a'] . " vs " . $match['team_b']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </label><br>

                <label>Typ zakładu:
                    <select name="bet_type" required>
                        <option value="team_a">Drużyna A</option>
                        <option value="draw">Remis</option>
                        <option value="team_b">Drużyna B</option>
                    </select>
                </label><br>

                <label>Stawka: <input type="number" step="0.01" name="stake" required></label><br>

                <button type="submit" name="place_bet">Postaw zakład</button>
            </form>
        </section>

        <section>
<h2>Twoje Zakłady</h2>
<p>Aktualna liczba żetonów: <?php echo $balance; ?></p>
<?php if (empty($bets)): ?>
    <p>Nie masz żadnych aktywnych zakładów.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Mecz</th>
            <th>Twój typ</th>
            <th>Stawka</th>
            <th>Potencjalna wygrana</th>
        </tr>
        <?php foreach ($bets as $bet): ?>
            <tr>
                <td><?php echo htmlspecialchars($bet['team_a']) . " vs " . htmlspecialchars($bet['team_b']); ?></td>
                <td><?php echo htmlspecialchars($bet['bet_type']); ?></td>
                <td><?php echo htmlspecialchars($bet['stake']); ?> zł</td>
                <td><?php echo htmlspecialchars($bet['potential_win']); ?> zł</td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
</section>
    </main>

    <footer>
        <p>&copy; 2025 Betting Site. Wszelkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>
