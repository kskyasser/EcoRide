<?php
declare(strict_types=1);

// Connexion PDO
require_once __DIR__ . '/../config/db.php';
$pdo = get_pdo();

// Récupérer les trajets (ordre par date)
$stmt  = $pdo->query("SELECT id, departure, destination, trip_date, seats_available, driver
                      FROM trips ORDER BY trip_date ASC");
$trips = $stmt->fetchAll();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>EcoRide – Trajets</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:system-ui,Arial,sans-serif;max-width:960px;margin:24px auto;padding:0 16px}
    h1{color:#2e8b57}
    table{border-collapse:collapse;width:100%;margin-top:16px}
    th,td{border:1px solid #ddd;padding:8px;text-align:center}
    th{background:#f3f3f3}
    .empty{color:#666}
  </style>
</head>
<body>
  <h1>🚗🌱 Trajets disponibles</h1>

  <?php if (!$trips): ?>
    <p class="empty">Aucun trajet pour le moment.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Départ</th>
          <th>Arrivée</th>
          <th>Date</th>
          <th>Places</th>
          <th>Conducteur</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($trips as $t): ?>
          <tr>
            <td><?= (int)$t['id'] ?></td>
            <td><?= htmlspecialchars($t['departure']) ?></td>
            <td><?= htmlspecialchars($t['destination']) ?></td>
            <td><?= htmlspecialchars($t['trip_date']) ?></td>
            <td><?= (int)$t['seats_available'] ?></td>
            <td><?= htmlspecialchars($t['driver']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</body>
</html>
