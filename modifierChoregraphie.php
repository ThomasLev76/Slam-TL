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

$stmt = $pdo->prepare("SELECT * FROM chorégraphie WHERE id = :id");
$stmt->execute([':id' => $id]);
$chore = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<h1>Modifier la chorégraphie</h1>

<form method="post" action="actions/updateChoregraphie.php">
    <input type="hidden" name="id" value="<?= $chore['id'] ?>">
    <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">

    <label>Nom</label><br>
    <input type="text" name="nom" value="<?= htmlspecialchars($chore['nom']) ?>" required><br><br>

    <label>Position du bras (choisir par ID)</label><br>
    <select name="position_bras_id" required>
        <?php foreach ($positions as $pos): ?>
            <option value="<?= $pos['id'] ?>" <?= $pos['id'] == $chore['position_bras_id'] ? 'selected' : '' ?>>
                <?= $pos['id'] ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Durée du mouvement (en seconde)</label><br>
    <input type="text" name="duree_mouv" value="<?= htmlspecialchars($chore['duree_mouv']) ?>" required><br><br>


    <label>Message à afficher sur l’écran</label><br>
    <input type="text" name="ecran" value="<?= htmlspecialchars($chore['ecran']) ?>"><br><br>

    <?php
    $audioDir = __DIR__ . '/son';
    $audioFiles = [];

    if (is_dir($audioDir)) {
        $files = scandir($audioDir);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'mp3') {
                $audioFiles[] = $file;
            }
        }
    }
    ?>
    <div class="mb-3">
        <label class="form-label">Son</label>
        <select name="son" class="form-select w-50"><br>
            <option value="">— Aucun son —</option>
            <?php foreach ($audioFiles as $file): ?>
                <option value="<?= htmlspecialchars($file) ?>"
                        <?= (!empty($chore['son']) && $chore['son'] === $file) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($file) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <br><br>

    <input
            type="range"
            class="form-range"
            id="volume"
            name="volume"
            min="0"
            max="100"
            value="<?= (int)$chore['volume'] ?>"
>
    <span id="volumeValue"><?= (int)$chore['volume'] ?></span> %

    <script>
        const volume = document.getElementById('volume');
        const volumeValue = document.getElementById('volumeValue');

        volume.addEventListener('input', () => {
            volumeValue.textContent = volume.value;
        });
    </script>
    <br>

    <button type="submit">Enregistrer les modifications</button>
</form>

<a href="index.php">Retour à la liste</a>
