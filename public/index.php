<?php
declare(strict_types=1);

// Connexion PDO
require_once __DIR__ . '/../config/db.php';
$pdo = get_pdo();

// Récupérer les trajets (ordre par date)

// --- Recherche (facultative) ---
$q = trim($_GET['q'] ?? '');

// Prépare la requête selon la présence d'un mot-clé
if ($q !== '') {
    $sql  = "SELECT * FROM trips
             WHERE departure LIKE :q OR destination LIKE :q OR driver LIKE :q
             ORDER BY trip_date ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':q' => "%{$q}%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM trips ORDER BY trip_date ASC");
}
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

<?php if (isset($_GET['added'])): ?>
  <p style="background:#e7f7ec;border:1px solid #a8e0b5;color:#136b2d;padding:10px;border-radius:8px">
    ✅ Trajet ajouté avec succès.
  </p>
<?php endif; ?>

<?php if (!empty($_GET['flash'])): ?>
  <p style="background:#e8fff3;border:1px solid #b6f0cd;padding:8px;border-radius:6px;max-width:920px">
    <?= $_GET['flash']==='added'?'Trajet ajouté.':($_GET['flash']==='updated'?'Trajet modifié.':($_GET['flash']==='deleted'?'Trajet supprimé.':'')) ?>
  </p>
<?php endif; ?>


  <?php if (!$trips): ?>
    <p class="empty">Aucun trajet pour le moment.</p>
  <?php else: ?>

<form method="get" style="margin:12px 0; display:flex; gap:8px; align-items:center;">
  <input name="q" value="<?= htmlspecialchars($q ?? '') ?>" placeholder="Rechercher (Paris, Nice, Alice…)"
         style="padding:6px 10px; border:1px solid #ccc; border-radius:6px; flex:1;">
  <button type="submit" style="padding:6px 10px; border:1px solid #2e8b57; background:#2e8b57; color:#fff; border-radius:6px;">
    Rechercher
  </button>
  <?php if (($q ?? '') !== ''): ?>
    <a href="index.php" style="color:#555; text-decoration:none;">Annuler</a>
  <?php endif; ?>
</form>

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Départ</th>
          <th>Arrivée</th>
          <th>Date</th>
          <th>Places</th>
          <th>Conducteur</th>
          <th>Action</th>
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
            <td><a href="edit.php?id=<?= (int)$t['id'] ?>">✏️ Modifier</a>
  <form action="delete.php" method="post" style="display:inline" 
        onsubmit="return confirm('Supprimer ce trajet ?');">
    <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
    <button type="submit" style="
      background:none;
      border:none;
      color:#b00;
      cursor:pointer;
      text-decoration:underline;">
      🗑️ Supprimer
    </button>
  </form>
</td>

          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
<p style="margin-top:16px"><a href="add.php">➕ Ajouter un trajet</a></p>
</body>
</html>
