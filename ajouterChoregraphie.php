<?php
session_start();

include "header.php";

$token=rand(0,1000000);
$_SESSION['token']=$token;
?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="card-title mb-4">Ajouter une chorégraphie</h2>

            <form action="actions/addChoregraphie.php" method="post">

                <!-- TOKEN CSRF -->
                <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">

                <div class="mb-3">
                    <label for="nom" class="form-label">Nom de la chorégraphie *</label>
                    <input type="text" name="nom" id="nom" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="valeur" class="form-label">Position du bras </label>
                    <input type="text" name="valeur" id="valeur" class="form-control" >
                </div>

                <div class="mb-3">
                    <label for="duree_mouv" class="form-label">Durée du mouvement (en seconde)</label>
                    <input type="number" name="duree_mouv" id="duree_mouv" class="form-control" required min="1">
                </div>

                <div class="mb-3">
                    <label for="ecran" class="form-label">Message à afficher sur l’écran</label>
                    <input type="text" name="ecran" id="ecran" class="form-control">
                </div>

                <?php
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
                ?>

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
                        Volume : <span id="volumeValue">50</span> %
                    </label>
                    <input type="range" class="form-range" id="volume" name="volume" min="0" max="100" value="50" style="width: 300px;">
                </div>

                <script>
                    const volume = document.getElementById('volume');
                    const volumeValue = document.getElementById('volumeValue');
                    volume.addEventListener('input', () => {
                        volumeValue.textContent = volume.value;
                    });
                </script>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success me-2">Enregistrer</button>
                    <a href="index.php" class="btn btn-secondary">Retour à la liste</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
