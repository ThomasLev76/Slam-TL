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
<table class="table table-stripped">
    <tr>
        <th>id</th>
        <th>Nom</th>
        <th>Son</th>
        <th>Écran</th>
        <th>Position bras</th>
        <th>Durée du mouvement</th>

    </tr>
    <?php
    foreach ($choregraphies as $choregraphie) {
        ?>
        <tr>
            <td><?php echo $choregraphie["id"] ?></td>
            <td><?php echo $choregraphie["nom"] ?></td>
            <td><?php echo $choregraphie["son"]?></td>
            <td><?php echo $choregraphie["ecran"]?></td>
            <td><?php echo $choregraphie["position_bras_id"] ?></td>
            <td><?php echo $choregraphie["duree_mouv"] ?></td>

            <td>
                <a href="modifierChoregraphie.php?id=<?php echo $choregraphie["id"] ?>"
                   class="btn btn-sm btn-warning">Modifier</a>
                <a href="supprimerChoregraphie.php?id=<?php echo $choregraphie["id"] ?>" class="btn btn-sm btn-danger">Supprimer</a>


            </td>
        </tr>
        <?php
    }
    ?>
</table>
<a href="ajouterChoregraphie.php" class="btn btn-success">Ajouter</a>
<?php
include "footer.php";
?>






