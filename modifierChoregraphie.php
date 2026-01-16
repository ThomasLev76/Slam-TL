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

// Récupérer la chorégraphie
$stmt = $pdo->prepare("SELECT * FROM chorégraphie WHERE id = :id");
$stmt->execute([':id' => $id]);
$chore = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$chore) die("Chorégraphie introuvable");

// Récupérer les positions de bras
$positions = $pdo->query("SELECT id FROM position_bras")->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les fichiers audio
$audioDir = __DIR__ . '/son';
$audioFiles = [];

if (is_dir($audioDir)) {
    foreach (scandir($audioDir) as $file) {
        if ($file === '.' || $file === '..') continue;
        if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'mp3') {
            $audioFiles[] = $file;
        }
    }
}

include "header.php";
?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="card-title mb-4">Modifier la chorégraphie</h2>

            <form method="post" action="actions/updateChoregraphie.php">

                <!-- ID et TOKEN CSRF -->
                <input type="hidden" name="id" value="<?= $chore['id'] ?>">
                <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">

                <div class="mb-3">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" name="nom" id="nom" class="form-control"
                           value="<?= htmlspecialchars($chore['nom']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="position_bras_id" class="form-label">Position du bras (choisir par ID)</label>
                    <select name="position_bras_id" id="position_bras_id" class="form-select" required>
                        <?php foreach ($positions as $pos): ?>
                            <option value="<?= $pos['id'] ?>"
                                    <?= $pos['id'] == $chore['position_bras_id'] ? 'selected' : '' ?>>
                                <?= $pos['id'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="duree_mouv" class="form-label">Durée du mouvement (en secondes)</label>
                    <input type="number" name="duree_mouv" id="duree_mouv" class="form-control"
                           value="<?= htmlspecialchars($chore['duree_mouv']) ?>" required min="1">
                </div>

                <div class="mb-3">
                    <label for="ecran" class="form-label">Message à afficher sur l’écran</label>
                    <input type="text" name="ecran" id="ecran" class="form-control"
                           value="<?= htmlspecialchars($chore['ecran']) ?>">
                </div>

                <div class="mb-3">
                    <label for="son" class="form-label">Son</label>
                    <select name="son" id="son" class="form-select w-50">
                        <option value="">— Aucun son —</option>
                        <?php foreach ($audioFiles as $file): ?>
                            <option value="<?= htmlspecialchars($file) ?>"
                                    <?= (!empty($chore['son']) && $chore['son'] === $file) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($file) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="volume" class="form-label">
                        Volume : <span id="volumeValue"><?= (int)$chore['volume'] ?></span> %
                    </label>
                    <input type="range" class="form-range" id="volume" name="volume"
                           min="0" max="100" value="<?= (int)$chore['volume'] ?>" style="width: 300px;">
                </div>

                <script>
                    const volume = document.getElementById('volume');
                    const volumeValue = document.getElementById('volumeValue');
                    volume.addEventListener('input', () => {
                        volumeValue.textContent = volume.value;
                    });
                </script>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success me-2">Enregistrer les modifications</button>
                    <a href="index.php" class="btn btn-secondary">Retour à la liste</a>
                </div>

            </form>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
