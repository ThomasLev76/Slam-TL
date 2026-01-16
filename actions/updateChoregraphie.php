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
$position_bras = (int)($_POST['position_bras'] ?? 0);
$son = trim($_POST['son'] ?? '');
$volume = isset($_POST['volume']) ? (int)$_POST['volume'] : 50;
$duree = trim($_POST['duree_mouv'] ?? '');




if (!$id || !$nom || !$position_bras) {
    die("Champs obligatoires manquants");
}

// Sécurité
$son = $_POST['son'] ?? '';

if ($son !== '') {
    $son = basename($son); // bloque ../
    $audioPath = __DIR__ . '/../son/' . $son;

    if (!file_exists($audioPath)) {
        die("Fichier audio invalide");
    }
}

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



    // Mettre à jour la chorégraphie
    $update = $pdo->prepare("
        UPDATE chorégraphie
        SET nom = :nom, `ecran` = :ecran, position_bras = :pos_bras, son = :son, volume = :volume, duree_mouv = :duree
        WHERE id = :id
    ");
    $update->execute([
        ':nom' => $nom,
        ':ecran' => $ecran,
        ':pos_bras' => $position_bras,
        ':son' => $son,
        ':id' => $id,
        ':volume' => $volume,
         ':duree' => $duree
    ]);

    header("Location: ../index.php");
    exit;

} catch (PDOException $e) {
    die("Erreur BDD : " . $e->getMessage());
}
