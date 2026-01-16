<?php
session_start();
require_once "../config.php";

$tokenServeur= $_SESSION['token'];
$tokenRecu=filter_input(INPUT_POST, 'token', FILTER_DEFAULT);

//je vérifie la cohérence des tokens
if($tokenRecu != $tokenServeur){
    die("Erreur de token. Va mourir vilain hacker.");//je stoppe tout
}

// Récupérer et nettoyer les champs
$id = (int)($_POST['id'] ?? 0);
$nom = trim($_POST['nom'] ?? '');
$ecran = trim($_POST['ecran'] ?? '');
$position_bras_id = (int)($_POST['position_bras_id'] ?? 0);
$son = trim($_POST['son'] ?? '');
$volume = isset($_POST['volume']) ? (int)$_POST['volume'] : 50;




if (!$id || !$nom || !$position_bras_id) {
    die("Champs obligatoires manquants");
}
// Sécurité
if ($volume < 0 || $volume > 100) {
    die("Volume invalide");
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
        SET nom = :nom, `ecran` = :ecran, position_bras_id = :pos_id, son = :son, volume = :volume
        WHERE id = :id
    ");
    $update->execute([
        ':nom' => $nom,
        ':ecran' => $ecran,
        ':pos_id' => $position_bras_id,
        ':son' => $son,
        ':id' => $id,
        ':volume' => $volume
    ]);

    header("Location: ../index.php");
    exit;

} catch (PDOException $e) {
    die("Erreur BDD : " . $e->getMessage());
}
