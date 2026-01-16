<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once "../config.php";

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

// Vérifier méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Requête invalide");
}

// Vérifier
if (!isset($_POST['token']) || $_POST['token'] != $_SESSION['token']) {
    die("Token invalide");
}
// Vérifier ID
    $id = (int)($_POST['id'] ?? 0);

    if (!$id) {
        die("ID manquant");
    }

    $pdo = new PDO(
        'mysql:host=' . config::HOST . ';dbname=' . config::DBNAME,
        config::USER,
        config::PASSWORD
    );

// Récupérer la chorégraphie
    $stmt = $pdo->prepare("SELECT * FROM chorégraphie WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $chore = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$chore) {
        die("Chorégraphie introuvable");
    }

    // 🔹 Construire le payload JSON
    $payload = [
        'nom'           => $chore['nom'],
        'position_bras' => (int)$chore['position_bras'],
        'ecran'         => $chore['ecran'],
        'son'           => $chore['son'],
        'volume'        => (int)$chore['volume'],
        'duree_mouv'    => (int)$chore['duree_mouv']
    ];

    $json = json_encode($payload, JSON_UNESCAPED_UNICODE);

    // 🔹 Connexion MQTT
    $mqtt = new MqttClient(
        '172.16.115.1',          // Adresse du broker
        1883,                 // Port MQTT
        'bisik-web-' . uniqid()
    );

    $settings = (new ConnectionSettings)
        ->setKeepAliveInterval(60);

    $mqtt->connect($settings, true);

    // 🔹 Publication du message
    $mqtt->publish('bisik/notification', $json, 0);

    $mqtt->disconnect();

    // Redirection après succès
    header("Location: ../index.php?sent=1");
    exit;


