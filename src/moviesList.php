<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . './includes/Manager.php';

$isUserAdmin = false;

session_start();
// si l'utilisateur est pas connecté et qu'il est amdinistrateur
if (array_key_exists("user", $_SESSION) and $_SESSION['user'] == 'admin@adm.adm') {
    $isUserAdmin = true;
}

// Pour renvoyer vers le code html (vue)
require 'views/viewMoviesList.php';

?>

