<?php
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
        <th>Text à afficher</th>

    </tr>
    <?php
    foreach ($choregraphies as $choregraphie) {
        ?>
        <tr>
            <td><?php echo $choregraphie["id"] ?></td>
            <td><?php echo $choregraphie["nom"] ?></td>
            <td><?php echo $choregraphie["text"]?></td>

            <td>
                <a href="modifierChoregraphie.php?id=<?php echo $choregraphie["id"] ?>"
                   class="btn btn-sm btn-warning">Modifier</a>
                <a href="supprimerChoregraphie.php?id=<?php echo $choregraphie["id"] ?>"
                   class="btn btn-sm btn-danger">Supprimer</a>

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






