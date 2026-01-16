<?php
session_start();
$id = (int)($_GET['id'] ?? 0);
if (!$id) die("ID manquant");

$_SESSION['token'] = $_SESSION['token'] ?? bin2hex(random_bytes(32));
?>

<h2>Confirmer la suppression</h2>
<form action="actions/deleteChoregraphie.php" method="post">
    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
    <button onclick="return confirm('Supprimer cette chorégraphie ?')">Confirmer</button>
</form>
<a href="index.php">Annuler</a>
