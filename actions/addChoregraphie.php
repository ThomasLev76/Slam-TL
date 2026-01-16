<?php
session_start();

// Vérification du token CSRF
if (!isset($_POST['token']) || $_POST['token'] != $_SESSION['token']) {
    die("Token invalide");
}

// Récupération et nettoyage des champs POST
$nom = filter_input(INPUT_POST, 'nom', FILTER_DEFAULT);
$ecran = filter_input(INPUT_POST, 'ecran', FILTER_DEFAULT);
$positionBras = filter_input(INPUT_POST, 'valeur', FILTER_DEFAULT);
$son = filter_input(INPUT_POST, 'son', FILTER_DEFAULT);
$volume = filter_input(INPUT_POST, 'volume', FILTER_DEFAULT);

// Nettoyage du JSON
$positionBras = trim($positionBras);

// Vérification que le JSON est valide
json_decode($positionBras);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("JSON invalide : " . json_last_error_msg());
}


include "../config.php";


    $pdo = new PDO(
        'mysql:host=' . config::HOST . ';dbname=' . config::DBNAME,
        config::USER,
        config::PASSWORD,
);


    // Insertion de la position du bras
    $stmt1 = $pdo->prepare("INSERT INTO position_bras (valeur) VALUES (:valeur)");
    $stmt1->bindParam(':valeur', $positionBras);
    $stmt1->execute();

    // Récupération de l'ID généré
    $position_id = $pdo->lastInsertId();

    // Insertion de la chorégraphie
    $stmt2 = $pdo->prepare("INSERT INTO chorégraphie (nom, ecran, position_bras_id, son, volume) 
                            VALUES (:nom, :ecran, :position_bras_id, :son, :volume)");
    $stmt2->bindParam(':volume', $volume);
    $stmt2->bindParam(':ecran', $ecran);
    $stmt2->bindParam(':son', $son);
    $stmt2->bindParam(':nom', $nom);;
    $stmt2->bindParam(':position_bras_id', $position_id);
    $stmt2->execute();

header("Location: ../index.php");
?>
