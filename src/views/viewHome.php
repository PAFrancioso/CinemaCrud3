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
        <title>Cinéma CRUD</title>
        <link type="text/css" href="css/cinema.css" rel="stylesheet"/>
    </head>
    <body>
        <div>
            <header>
                <h1>Espace personnel</h1>
            </header>
            <?php
            // si pas encore authentifié
            if (!$loginSuccess):
                ?>
                <form method="POST" name="editFavoriteMoviesList" action="index.php">

                    <label>Adresse email : </label>
                    <input type="email" name="email" required/>
                    <label>Mot de passe  : </label>
                    <input type="password" name="password" required/>
                    <div class="error">
                        <?php
                        if (!$areCredentialsOK):
                            echo "Les informations de connexions ne sont pas correctes.";
                        endif;
                        ?>
                    </div>
                    <input type="submit" value="Editer ma liste de films préférés"/>
                </form>
                <p>Pas encore d'espace personnel ? <a href="index.php?action=createNewUser">Créer sa liste de films préférés.</a></p>
                <?php
            // sinon (utilisateur authentifié)
            else:
                ?>
                <form action="index.php">
                    <input type="hidden" value="editFavoriteMoviesList" name="action">
                    <input type="submit" value="Editer ma liste de films préférés"/>
                </form>
                <a href="index.php?action=logout">Se déconnecter</a>
            <?php endif; ?>
        </div>
        <!-- Gestion des cinémas -->
        <div>
            <header>
                <h1>Gestion des cinémas</h1>
                <form name="cinemasList" action="index.php">
                    <input name="action" type="hidden" value="cinemasList"/>
                    <input type="submit" value="Consulter la liste des cinémas"/>
                </form>
                <form name="moviesList" action="index.php">
                    <input name="action" type="hidden" value="moviesList"/>
                    <input type="submit" value="Consulter la liste des films"/>
                </form>
            </header>
        </div>
    </body>
</html>