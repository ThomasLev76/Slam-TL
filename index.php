<?php
session_start();
$_SESSION['token'] = $_SESSION['token'] ?? bin2hex(random_bytes(32));

include "header.php";
?>


<h1>Bienvenue sur votre page pour gérer vos chorégraphies</h1>

<?php
include_once "config.php";
$pdo = new PDO('mysql:host=' . config::HOST . ';dbname=' . config::DBNAME
    , config::USER, config::PASSWORD);


$req = $pdo->prepare("select * from chorégraphie");
$req->execute();
$choregraphies = $req->fetchAll();
?>
<div class="container mt-4">
    <h1 class="mb-4">Vos chorégraphies</h1>

    <div class="row">
        <?php foreach ($choregraphies as $chore): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($chore['nom']) ?></h5>

                        <!-- Son -->
                        <?php if (!empty($chore['son'])): ?>
                            <p class="card-text mb-2">
                                <strong>Son :</strong> <?= htmlspecialchars($chore['son']) ?>
                            </p>
                            <audio controls class="w-100 mb-2">
                                <source src="son/<?= htmlspecialchars($chore['son']) ?>" type="audio/mpeg">
                            </audio>
                        <?php endif; ?>

                        <!-- Écran -->
                        <p class="card-text mb-2"><strong>Écran :</strong> <?= htmlspecialchars($chore['ecran']) ?></p>

                        <!-- Position bras -->
                        <p class="card-text mb-2"><strong>Position bras :</strong> <?= $chore['position_bras'] ?>°</p>

                        <!-- Durée -->
                        <p class="card-text mb-2"><strong>Durée :</strong> <?= $chore['duree_mouv'] ?> s</p>

                        <div class="mt-auto d-flex justify-content-between">
                            <a href="modifierChoregraphie.php?id=<?= $chore['id'] ?>"
                               class="btn btn-sm btn-warning">Modifier</a>
                            <a href="supprimerChoregraphie.php?id=<?= $chore['id'] ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Supprimer cette chorégraphie ?')">Supprimer</a>
                            <a href="envoyerChoregraphie.php?id=<?= $chore['id'] ?>"
                               class="btn btn-sm btn-success">Envoyer</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <a href="ajouterChoregraphie.php" class="btn btn-success mt-3">Ajouter une chorégraphie</a>
</div>


<?php
include "footer.php";
?>






