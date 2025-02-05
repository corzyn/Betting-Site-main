<?php
require 'db.php';

session_start();

// Sprawdzanie, czy użytkownik jest zalogowany i czy ma uprawnienia administratora
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// Obsługa aktualizacji statusu meczu i przyznawania żetonów
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $match_id = $_POST['match_id'];
    $score_team_a = $_POST['score_team_a'];
    $score_team_b = $_POST['score_team_b'];

    try {
        $pdo->beginTransaction();

        // Dodanie wyniku meczu
        $stmt = $pdo->prepare("
            INSERT INTO results (match_id, score_team_a, score_team_b)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE score_team_a = VALUES(score_team_a), score_team_b = VALUES(score_team_b)
        ");
        $stmt->execute([$match_id, $score_team_a, $score_team_b]);

        // Pobranie zakładów na dany mecz
        $stmtBets = $pdo->prepare("SELECT * FROM bets WHERE match_id = ?");
        $stmtBets->execute([$match_id]);
        $bets = $stmtBets->fetchAll();

        foreach ($bets as $bet) {
            $bet_result = 'przegrany';
            $reward = 0;

            // Ustalanie wyniku zakładu
            if (($bet['bet_type'] == 'team_a' && $score_team_a > $score_team_b) ||
                ($bet['bet_type'] == 'team_b' && $score_team_b > $score_team_a) ||
                ($bet['bet_type'] == 'draw' && $score_team_a == $score_team_b)) {
                $bet_result = 'wygrany';
                $reward = $bet['potential_win'];
            }

            // Aktualizacja statusu zakładu
            $updateBetStmt = $pdo->prepare("UPDATE bets SET status = ? WHERE id = ?");
            $updateBetStmt->execute([$bet_result, $bet['id']]);

            // Przyznanie żetonów w przypadku wygranej
            if ($bet_result == 'wygrany') {
                $updateUserStmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
                $updateUserStmt->execute([$reward, $bet['user_id']]);
            }
        }

        $pdo->commit();
        $success = "Wynik meczu został zaktualizowany, a żetony zostały przyznane!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Wystąpił błąd: " . $e->getMessage();
    }
}

// Pobieranie listy meczów
$stmt = $pdo->query("SELECT * FROM matches");
$matches = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Administratora</title>
</head>
<body>
    <h1>Panel Administratora</h1>

    <?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <h2>Zaktualizuj Wynik Meczu</h2>
    <form method="POST">
        <label>Wybierz mecz:
            <select name="match_id" required>
                <?php foreach ($matches as $match): ?>
                    <option value="<?php echo $match['id']; ?>">
                        <?php echo $match['team_a'] . " vs " . $match['team_b']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>Wynik Drużyna A: <input type="number" name="score_team_a" required></label><br>
        <label>Wynik Drużyna B: <input type="number" name="score_team_b" required></label><br>
        <button type="submit" name="update_status">Zaktualizuj Wynik</button>
    </form>

    <a href="index.php">Powrót do strony głównej</a>
</body>
</html>
