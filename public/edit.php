<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/db.php';
$pdo = get_pdo();

$id = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { http_response_code(400); exit('ID invalide'); }

// Charger le trajet
$stmt = $pdo->prepare("SELECT * FROM trips WHERE id = :id");
$stmt->execute([':id' => $id]);
$trip = $stmt->fetch();
if (!$trip) { http_response_code(404); exit('Trajet introuvable'); }

$errors = [];
$values = [
  'departure' => $trip['departure'],
  'destination' => $trip['destination'],
  'trip_date' => $trip['trip_date'],
  'seats_available' => (string)$trip['seats_available'],
  'driver' => $trip['driver'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($values as $k => $_) { $values[$k] = trim($_POST[$k] ?? ''); }

  if ($values['departure'] === '') $errors['departure'] = 'Départ requis';
  if ($values['destination'] === '') $errors['destination'] = 'Arrivée requise';
  if ($values['driver'] === '') $errors['driver'] = 'Conducteur requis';
  if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $values['trip_date'])) $errors['trip_date'] = 'Date invalide';
  if (!ctype_digit($values['seats_available']) || (int)$values['seats_available'] < 1) $errors['seats_available'] = 'Places invalides';

  if (!$errors) {
    $stmt = $pdo->prepare("
      UPDATE trips
      SET departure=:departure, destination=:destination, trip_date=:trip_date, seats_available=:seats, driver=:driver
      WHERE id=:id
    ");
    $stmt->execute([
      ':departure' => $values['departure'],
      ':destination' => $values['destination'],
      ':trip_date' => $values['trip_date'],
      ':seats' => (int)$values['seats_available'],
      ':driver' => $values['driver'],
      ':id' => $id,
    ]);
    header('Location: /index.php?flash=updated', true, 303);
    exit;
  }
}
?>
<!doctype html>
<html lang="fr"><meta charset="utf-8"><title>Modifier le trajet #<?= $id ?></title>
<body style="font-family: system-ui, Arial; max-width: 920px; margin: 24px auto">
  <h1>✏️ Modifier le trajet #<?= $id ?></h1>

  <?php if ($errors): ?>
    <p style="color:#b00020">Veuillez corriger les erreurs.</p>
  <?php endif; ?>

  <form method="post" style="display:grid; gap:12px; max-width:520px">
    <label>Départ
      <input name="departure" value="<?=htmlspecialchars($values['departure'])?>" required>
      <small style="color:#b00020"><?= $errors['departure'] ?? '' ?></small>
    </label>

    <label>Arrivée
      <input name="destination" value="<?=htmlspecialchars($values['destination'])?>" required>
      <small style="color:#b00020"><?= $errors['destination'] ?? '' ?></small>
    </label>

    <label>Date
      <input name="trip_date" value="<?=htmlspecialchars($values['trip_date'])?>" required>
      <small style="color:#b00020"><?= $errors['trip_date'] ?? '' ?></small>
    </label>

    <label>Places
      <input name="seats_available" type="number" min="1" value="<?=htmlspecialchars($values['seats_available'])?>" required>
      <small style="color:#b00020"><?= $errors['seats_available'] ?? '' ?></small>
    </label>

    <label>Conducteur
      <input name="driver" value="<?=htmlspecialchars($values['driver'])?>" required>
      <small style="color:#b00020"><?= $errors['driver'] ?? '' ?></small>
    </label>

    <div>
      <button type="submit">Enregistrer</button>
      <a href="/index.php">Annuler</a>
    </div>
  </form>
</body></html>

