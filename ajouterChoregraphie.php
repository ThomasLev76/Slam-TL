<?php
session_start();

include "header.php";

$token=rand(0,1000000);
$_SESSION['token']=$token;
?>

<h1>Ajouter une chorégraphie</h1>

<form action="actions/addChoregraphie.php" method="post">

    <!-- TOKEN CSRF -->
    <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">

    <label>Nom de la chorégraphie *</label><br>
    <input type="text" name="nom" required><br><br>

    <label>Position du bras (en JSON)</label><br>
    <input type="text" name="valeur"
           placeholder='Exemple : {"angle": 45}'><br><br>

    <label>Message à afficher sur l’écran</label><br>
    <input type="text" name="ecran"><br><br>

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
        <select name="son" class="form-select w-50">
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

    <div class="mb-3">
        <label for="volume" class="form-label">
            Volume : <span id="volumeValue">50</span> %
        </label><br>
        <input
                type="range"
                class="form-range"
                id="volume"
                name="volume"
                min="0"
                max="100"
                value="50"
                style="width: 300px;"
        >
    </div>

    <script>
        const volume = document.getElementById('volume');
        const volumeValue = document.getElementById('volumeValue');

        volume.addEventListener('input', () => {
            volumeValue.textContent = volume.value;
        });
    </script>

    <button type="submit">Enregistrer</button>
</form>

<a href="index.php">Retour à la liste</a>

</body>
</html>
<?php
include "footer.php";
?>
