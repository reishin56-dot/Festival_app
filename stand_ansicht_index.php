<?php
require 'session.php';
require 'php_functions.php';
nurFuerAdmin();

$stand_id = (int)($_GET['stand'] ?? 1);

// Status-Update via POST (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bestellung_id'], $_POST['status'])) {
    bestellungStatusAktualisieren($_POST['bestellung_id'], $_POST['status']);
    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit;
}

$staende      = getAlleStaende();
$bestellungen = getOffeneBestellungenByStand($stand_id);

$statusFlow = ['offen' => 'in_bearbeitung', 'in_bearbeitung' => 'abgeschlossen'];
$statusLabel = ['offen' => 'Erstellt', 'in_bearbeitung' => 'In Zubereitung', 'abgeschlossen' => 'Bereit'];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Standansicht</title>
    <link rel="stylesheet" href="style.css">
</head>



<body>
<div class="stand-card">
    <div class="stand-header">
        <div class="stand-title">
            <form method="get" style="display:inline;">
                Stand:
                <select name="stand" onchange="this.form.submit()" style="font-size:16px;">
                    <?php foreach ($staende as $s): ?>
                      <option value="<?= $s['id'] ?>" <?= $s['id'] == $stand_id ? 'selected' : '' ?>>
                          <?= htmlspecialchars($s['name']) ?>
                      </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <div class="stand-stock">Offen: <strong><?= count($bestellungen) ?></strong></div>
    </div>

    <div class="order-list">
        <?php if (empty($bestellungen)): ?>
          <p style="padding:20px;">Keine offenen Bestellungen.</p>
        <?php else: ?>
          <?php foreach ($bestellungen as $b): ?>
            <div class="order-item">
                <div class="order-top">
                    <span class="order-id">#<?= $b['id'] ?></span>
                    <span class="order-details"><?= htmlspecialchars($b['artikel']) ?></span>
                    <span class="order-time"><?= date('H:i', strtotime($b['bestellt_am'])) ?></span>
                </div>
                <div class="order-extra">Ticket: <?= htmlspecialchars($b['ticketnummer']) ?></div>
                <button class="status-btn"
                        data-id="<?= $b['id'] ?>"
                        data-status="<?= $b['status'] ?>"
                        onclick="nextStatus(this)">
                    <?= $statusLabel[$b['status']] ?? $b['status'] ?>
                </button>
            </div>
            <div class="divider"></div>
          <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <button class="back-btn" onclick="goToPage('engpasssteuerung.php')">Zurück</button>
</div>

<footer>
    <p>© 2026 Festival App – by Abilas Sivarajah & Lucas Kessler & Michael Linn</p>
</footer>

<script src="script.js"></script>
<script>
const statusFlow  = {offen: 'in_bearbeitung', in_bearbeitung: 'abgeschlossen'};
const statusLabel = {offen: 'Erstellt', in_bearbeitung: 'In Zubereitung', abgeschlossen: 'Bereit'};

function nextStatus(btn) {
    const id         = btn.dataset.id;
    const current    = btn.dataset.status;
    const next       = statusFlow[current];
    if (!next) return;

    fetch('stand_ansicht_index.php?stand=<?= $stand_id ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'bestellung_id=' + id + '&status=' + next
    }).then(() => {
        btn.dataset.status = next;
        btn.innerText      = statusLabel[next];
        if (next === 'abgeschlossen') {
            btn.style.background  = '#7dbb7f';
            btn.style.color       = '#fff';
            btn.style.borderColor = '#5a9a5c';
        } else {
            btn.style.background  = '#ffcc00';
            btn.style.borderColor = '#d4a400';
            btn.style.color       = '#1a1a1a';
        }
    });
}
</script>
</body>
</html>
