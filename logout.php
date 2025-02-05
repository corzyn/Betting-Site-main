<?php
// Rozpoczynamy sesję
session_start();

// Usuwamy wszystkie zmienne sesji
session_unset();

// Zniszczymy sesję
session_destroy();

// Przekierowujemy użytkownika na stronę logowania
header("Location: login.php");
exit();
?>
