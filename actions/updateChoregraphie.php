<?php
session_start();
require_once "../config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') die("Mauvaise requête");

// Vérifier CSRF
if (!isset($_POST['token'], $_SESSION['token']) || !hash_equals($_SESSION['token'], $_POST['token'])) {
    die("Token CSRF invalide");
}

// Récupérer et nettoyer les champs
$id = (int)($_POST['id'] ?? 0);
$nom = trim($_POST['nom'] ?? '');
$ecran = trim($_POST['ecran'] ?? '');
$position_bras_id = (int)($_POST['position_bras_id'] ?? 0);
$son = trim($_POST['son'] ?? '');

if (!$id || !$nom || !$position_bras_id) {
    die("Champs obligatoires manquants");
}

try {
    $pdo = new PDO(
        'mysql:host=' . config::HOST . ';dbname=' . config::DBNAME . ';charset=utf8mb4',
        config::USER,
        config::PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Vérifier que la position de bras existe
    $stmt = $pdo->prepare("SELECT id FROM position_bras WHERE id = :id");
    $stmt->execute([':id' => $position_bras_id]);
    if (!$stmt->fetch()) die("Position de bras invalide");

    // Mettre à jour la chorégraphie
    $update = $pdo->prepare("
        UPDATE chorégraphie
        SET nom = :nom, `ecran` = :ecran, position_bras_id = :pos_id, son = :son
        WHERE id = :id
    ");
    $update->execute([
        ':nom' => $nom,
        ':ecran' => $ecran,
        ':pos_id' => $position_bras_id,
        ':son' => $son,
        ':id' => $id
    ]);

    header("Location: ../index.php");
    exit;

} catch (PDOException $e) {
    die("Erreur BDD : " . $e->getMessage());
}
