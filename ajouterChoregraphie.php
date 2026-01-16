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

    <label>Son</label><br>
    <input type="text" name="son"><br><br>

    <button type="submit">Enregistrer</button>
</form>

<a href="index.php">Retour à la liste</a>

</body>
</html>
<?php
include "footer.php";
?>
