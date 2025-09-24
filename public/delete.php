<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/db.php';
$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  header('Allow: POST');
  exit('Méthode non autorisée');
}

$id = isset($_POST['id']) && ctype_digit($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) { http_response_code(400); exit('ID invalide'); }

$stmt = $pdo->prepare("DELETE FROM trips WHERE id = :id");
$stmt->execute([':id' => $id]);

header('Location: /index.php?flash=deleted', true, 303);
exit;

