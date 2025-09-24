<?php
declare(strict_types=1);

// 1) Connexion PDO
require_once __DIR__ . '/../config/db.php';
$pdo = get_pdo();

// 2) Si formulaire soumis -> valider + insérer
$errors = [];
$done   = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération/trim
    $departure = trim($_POST['departure'] ?? '');
    $destination = trim($_POST['destination'] ?? '');
    $trip_date = trim($_POST['trip_date'] ?? '');
    $seats_available = (int)($_POST['seats_available'] ?? 0);
    $driver = trim($_POST['driver'] ?? '');

    // Validations simples
    if ($departure === '')        $errors[] = "Le départ est obligatoire.";
    if ($destination === '')      $errors[] = "La destination est obligatoire.";
    if ($trip_date === '')        $errors[] = "La date est obligatoire.";
    if ($seats_available < 1)     $errors[] = "Le nombre de places doit être ≥ 1.";
    if ($driver === '')           $errors[] = "Le conducteur est obligatoire.";

    // Insertion si ok
    if (!$errors) {
        $sql = "INSERT INTO trips (departure, destination, trip_date, seats_available, driver)
                VALUES (:departure, :destination, :trip_date, :seats_available, :driver)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':departure'       => $departure,
            ':destination'     => $destination,
            ':trip_date'       => $trip_date,
            ':seats_available' => $seats_available,
            ':driver'          => $driver,
        ]);

        // redirection avec message
        header('Location: index.php?added=1');
        exit;
    }
}
?>
<!doctype html>
<html lang="fr">
<meta charset="utf-8">
<title>Ajouter un trajet • EcoRide</title>
<style>
  body{font-family:system-ui,Arial,sans-serif;max-width:920px;margin:24px auto;padding:0 12px}
  h1{color:#2e8b57}
  form{display:grid;gap:12px;max-width:520px}
  label{font-weight:600}
  input,button{font:inherit;padding:8px 10px}
  input{border:1px solid #bbb;border-radius:6px}
  .row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
  .errors{background:#ffecec;border:1px solid #f5a9a9;color:#990000;padding:10px;border-radius:8px}
  .actions{display:flex;gap:12px;align-items:center}
  a.btn{padding:8px 10px;border:1px solid #bbb;border-radius:6px;text-decoration:none;color:#222;background:#f6f6f6}
  button{background:#2e8b57;color:#fff;border:0;border-radius:6px;cursor:pointer}
  button:hover{filter:brightness(1.05)}
</style>

<body>
  <h1>➕ Ajouter un trajet</h1>

  <?php if ($errors): ?>
    <div class="errors">
      <strong>Veuillez corriger :</strong>
      <ul>
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach ?>
      </ul>
    </div>
  <?php endif ?>

  <form method="post" action="">
    <div class="row">
      <div>
        <label for="departure">Départ</label>
        <input id="departure" name="departure" value="<?= htmlspecialchars($_POST['departure'] ?? '') ?>" required>
      </div>
      <div>
        <label for="destination">Arrivée</label>
        <input id="destination" name="destination" value="<?= htmlspecialchars($_POST['destination'] ?? '') ?>" required>
      </div>
    </div>

    <div class="row">
      <div>
        <label for="trip_date">Date</label>
        <input id="trip_date" type="date" name="trip_date" value="<?= htmlspecialchars($_POST['trip_date'] ?? '') ?>" required>
      </div>
      <div>
        <label for="seats_available">Places</label>
        <input id="seats_available" type="number" min="1" name="seats_available"
               value="<?= htmlspecialchars($_POST['seats_available'] ?? '1') ?>" required>
      </div>
    </div>

    <div>
      <label for="driver">Conducteur</label>
      <input id="driver" name="driver" value="<?= htmlspecialchars($_POST['driver'] ?? '') ?>" required>
    </div>

    <div class="actions">
      <button type="submit">Enregistrer</button>
      <a class="btn" href="index.php">← Retour</a>
    </div>
  </form>
</body>
</html>
