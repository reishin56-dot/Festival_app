<?php
session_start();
require 'db.php';

$fehler = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticketnummer = trim($_POST['ticketnummer'] ?? '');
    if ($ticketnummer === '') {
        $fehler = 'Bitte Ticketnummer eingeben!';
    } else {
        $stmt = $pdo->prepare('SELECT id, name, credits FROM tickets WHERE ticketnummer = ?');
        $stmt->execute([$ticketnummer]);
        $ticket = $stmt->fetch();
        if ($ticket) {
            $_SESSION['ticket_id']    = $ticket['id'];
            $_SESSION['ticketnummer'] = $ticketnummer;
            $_SESSION['name']         = $ticket['name'];
            header('Location: menu.php');
            exit;
        } else {
            $fehler = 'Ungültige Ticketnummer!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Festival App</title>
  <link rel="stylesheet" href="style.css">
</head>


<body>
  <div class="container">
    <h1>Festival App</h1>
    <p>Bitte Ticketnummer eingeben:</p>

    <?php if ($fehler): ?>
      <p style="color:red;"><?= htmlspecialchars($fehler) ?></p>
    <?php endif; ?>

    <form method="post">
      <input type="text" name="ticketnummer" placeholder="Ticketnummer"
             value="<?= htmlspecialchars($_POST['ticketnummer'] ?? '') ?>">
      <br>
      <button type="submit">Weiter</button>
    </form>
    <br>
    <button onclick="window.location.href='role_selection.php'">Zurück zum Hauptmenü</button>
  </div>

  <footer>
    <p>© 2026 Festival App – by Abilas Sivarajah & Lucas Kessler & Michael Linn</p>
  </footer>

  <script src="script.js"></script>
</body>
</html>
