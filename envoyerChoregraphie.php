<?php
session_start();
include "config.php";

// Vérifier l'ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) die("ID manquant");

// Générer token CSRF
$_SESSION['token'] = $_SESSION['token'] ?? bin2hex(random_bytes(32));

// Connexion PDO
$pdo = new PDO('mysql:host=' . config::HOST . ';dbname=' . config::DBNAME
        , config::USER, config::PASSWORD);


$stmt = $pdo->prepare("SELECT * FROM chorégraphie WHERE id = :id");
$stmt->execute([':id' => $id]);
$chore = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$chore) die("Chorégraphie introuvable");

include "header.php";
?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h3>Envoyer la chorégraphie</h3>

            <ul class="list-group mb-3">
                <li class="list-group-item"><strong>Nom :</strong> <?= htmlspecialchars($chore['nom']) ?></li>
                <li class="list-group-item"><strong>Écran :</strong> <?= htmlspecialchars($chore['ecran']) ?></li>
                <li class="list-group-item"><strong>Son :</strong> <?= htmlspecialchars($chore['son']) ?></li>
                <li class="list-group-item"><strong>Volume :</strong> <?= $chore['volume'] ?>%</li>
                <li class="list-group-item"><strong>Durée Mouvement :</strong> <?= $chore['duree_mouv'] ?> s</li>
            </ul>

            <form action="actions/sendChoregraphie.php" method="post">
                <input type="hidden" name="id" value="<?= $chore['id'] ?>">
                <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">

                <button type="submit" class="btn btn-primary">
                    Confirmer l’envoi
                </button>
                <a href="index.php" class="btn btn-secondary ms-2">Annuler</a>
            </form>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
