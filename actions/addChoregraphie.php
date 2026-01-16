<?php
session_start();

// Vérification du token
if (!isset($_POST['token']) || $_POST['token'] != $_SESSION['token']) {
    die("Token invalide");
}

// Récupération et nettoyage des champs POST
$nom = filter_input(INPUT_POST, 'nom', FILTER_DEFAULT);
$ecran = filter_input(INPUT_POST, 'ecran', FILTER_DEFAULT);
$position_bras = filter_input(INPUT_POST, 'valeur', FILTER_DEFAULT);
$son = filter_input(INPUT_POST, 'son', FILTER_DEFAULT);
$volume = filter_input(INPUT_POST, 'volume', FILTER_DEFAULT);
$duree = filter_input(INPUT_POST, 'duree_mouv', FILTER_DEFAULT);

// Vérifier champs obligatoires
if (!$nom || !$position_bras) {
    die("Nom et position du bras obligatoires !");
}

include "../config.php";


    $pdo = new PDO(
        'mysql:host=' . config::HOST . ';dbname=' . config::DBNAME,
        config::USER,
        config::PASSWORD
);



    // Insertion de la chorégraphie
    $stmt2 = $pdo->prepare("INSERT INTO chorégraphie (nom, ecran, position_bras, son, volume, duree_mouv) 
                            VALUES (:nom, :ecran, :position_bras, :son, :volume, :duree_mouv)");
    $stmt2->bindParam(':duree_mouv', $duree);
    $stmt2->bindParam(':volume', $volume);
    $stmt2->bindParam(':ecran', $ecran);
    $stmt2->bindParam(':son', $son);
    $stmt2->bindParam(':nom', $nom);;
    $stmt2->bindParam(':position_bras', $position_bras);
    $stmt2->execute();

header("Location: ../index.php");
?>
