<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Cinéma - Editer un cinéma</title>
        <link rel="stylesheet" type="text/css" href="css/cinema.css"/>
    </head>
    <body>
        <h1>Ajouter/Modifier un cinéma</h1>
        <form method="POST" name="editCinema" action="editCinema.php">
            <label>Dénomination :</label>
            <input name="denomination" type="text" value="<?= $cinema['DENOMINATION'] ?>" required/>
            <label>Adresse :</label>
            <textarea name="adresse" required><?= $cinema['ADRESSE'] ?></textarea>
            <br/>
            <input type="hidden" value="<?= $cinema['CINEMAID'] ?>" name="cinemaID"/>
            <?php
            // si c'est une modification, c'est une information dont nous avons besoin
            if (!$isItACreation) {
                ?>
                <input type="hidden" name="modificationInProgress" value="true"/>
                <?php
            }
            ?>
            <input type="submit" name="saveEntry" value="Sauvegarder"/>
        </form>
        <form method="POST" action="editCinema.php">
        <input type="submit" name="backToList" value="Retour à la liste"/>
        </form>
    </body>
</html>