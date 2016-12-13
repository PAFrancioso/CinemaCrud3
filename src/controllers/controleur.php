<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 
 * @param type $managers Retour à l'accueil
 */
function home($managers){
    

// personne d'authentifié à ce niveau
$loginSuccess = false;

// variables de contrôle du formulaire
$areCredentialsOK = true;

// si l'utilisateur est déjà authentifié
if (array_key_exists("user",
                $_SESSION)) {
    $loginSuccess = true;
// Sinon (pas d'utilisateur authentifié pour l'instant)
} else {
    // si la méthode POST a été employée
    if (filter_input(INPUT_SERVER,
                    'REQUEST_METHOD') === "POST") {
        // on "sainifie" les entrées
        $sanitizedEntries = filter_input_array(INPUT_POST,
                ['email' => FILTER_SANITIZE_EMAIL,
            'password' => FILTER_DEFAULT]);
        try {
            // On vérifie l'existence de l'utilisateur
            $managers['utilisateursMgr']->verifyUserCredentials($sanitizedEntries['email'],
                    $sanitizedEntries['password']);

            // on enregistre l'utilisateur
            $_SESSION['user'] = $sanitizedEntries['email'];
            $_SESSION['userID'] = $managers['utilisateursMgr']->getUserIDByEmailAddress($_SESSION['user']);
            // on redirige vers la page d'édition des films préférés
            header("Location: index.php?action=editFavoriteMoviesList");
            exit;
        } catch (Exception $ex) {
            $areCredentialsOK = false;
            $logger->error($ex->getMessage());
        }
    }
}

// Pour renvoyer vers le code html de l'index.php, 
// coupé-collé dans viewHome.php (vue)
require 'views/viewHome.php';

}

/**
 * 
 * @param type $managers Affiche liste des cinemas
 */
function cinemasList($managers)
{
    
$isUserAdmin = false;

// si l'utilisateur est pas connecté et qu'il est amdinistrateur
if (array_key_exists("user", $_SESSION) and $_SESSION['user'] == 'admin@adm.adm') {
    $isUserAdmin = true;
}
require_once 'views/viewCinemaList.php';

}

function cinemaShowtimes($managers)
{
  $adminConnected = false;

// si l'utilisateur admin est connexté
if (array_key_exists("user", $_SESSION) and $_SESSION['user'] == 'admin@adm.adm') {
    $adminConnected = true;
}

// si la méthode de formulaire est la méthode GET
if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "GET") {

    // on assainie les entrées
    $sanitizedEntries = filter_input_array(INPUT_GET,
            ['cinemaID' => FILTER_SANITIZE_NUMBER_INT]);

    // si l'identifiant du cinéma a bien été passé en GET
    if ($sanitizedEntries && $sanitizedEntries['cinemaID'] !== NULL && $sanitizedEntries['cinemaID'] !=
            '') {
        // on récupère l'identifiant du cinéma
        $cinemaID = $sanitizedEntries['cinemaID'];
        // puis on récupère les informations du cinéma en question
        $cinema = $managers['cinemasMgr']->getCinemaInformationsByID($cinemaID);
        // on récupère les films pas encore projetés
        $filmsUnplanned = $managers['filmsMgr']->getNonPlannedMovies($cinemaID);
    }
    // sinon, on retourne à l'accueil
    else {
        header('Location: index.php');
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
require_once 'views/viewCinemaShowTimes.php';

}

function createNewUser($managers) {
    
// variables de contrôles du formulaire de création
$isFirstNameEmpty = false;
$isLastNameEmpty = false;
$isEmailAddressEmpty = false;
$isUserUnique = true;
$isPasswordEmpty = false;
$isPasswordConfirmationEmpty = false;
$isPasswordValid = true;

// si la méthode POST est utilisée, cela signifie que le formulaire a été envoyé
if (filter_input(INPUT_SERVER,
                'REQUEST_METHOD') === "POST") {
    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(INPUT_POST,
            ['firstName' => FILTER_SANITIZE_STRING,
        'lastName' => FILTER_SANITIZE_STRING,
        'email' => FILTER_SANITIZE_EMAIL,
        'password' => FILTER_DEFAULT,
        'passwordConfirmation' => FILTER_DEFAULT]);

    // si le prénom n'a pas été renseigné
    if ($sanitizedEntries['firstName'] === "") {
        $isFirstNameEmpty = true;
    }

    // si le nom n'a pas été renseigné
    if ($sanitizedEntries['lastName'] === "") {
        $isLastNameEmpty = true;
    }

    // si l'adresse email n'a pas été renseignée
    if ($sanitizedEntries['email'] === "") {
        $isEmailAddressEmpty = true;
    } else {
        // On vérifie l'existence de l'utilisateur
        $userID = $managers['utilisateursMgr']->getUserIDByEmailAddress($sanitizedEntries['email']);
        // si on a un résultat, cela signifie que cette adresse email existe déjà
        if ($userID) {
            $isUserUnique = false;
        }
    }
    // si le password n'a pas été renseigné
    if ($sanitizedEntries['password'] === "") {
        $isPasswordEmpty = true;
    }
    // si la confirmation du password n'a pas été renseigné
    if ($sanitizedEntries['passwordConfirmation'] === "") {
        $isPasswordConfirmationEmpty = true;
    }

    // si le mot de passe et sa confirmation sont différents
    if ($sanitizedEntries['password'] !== $sanitizedEntries['passwordConfirmation']) {
        $isPasswordValid = false;
    }

    // si les champs nécessaires ne sont pas vides, que l'utilisateur est unique et que le mot de passe est valide
    if (!$isFirstNameEmpty && !$isLastNameEmpty && !$isEmailAddressEmpty && $isUserUnique && !$isPasswordEmpty && $isPasswordValid) {
        // hash du mot de passe
        $password = password_hash($sanitizedEntries['password'],
                PASSWORD_DEFAULT);
        // créer l'utilisateur
        $managers['utilisateursMgr']->createUser($sanitizedEntries['firstName'],
                $sanitizedEntries['lastName'],
                $sanitizedEntries['email'],
                $password);

        session_start();
        // authentifier l'utilisateur
        $_SESSION['user'] = $sanitizedEntries['email'];
        $_SESSION['userID'] = $managers['utilisateursMgr']->getUserIDByEmailAddress($_SESSION['user']);
        // on redirige vers la page d'édition des films préférés
        header("Location: index.php?action=editFavoriteMoviesList");
        exit;
    }
}
// sinon (le formulaire n'a pas été envoyé)
else {
    // initialisation des variables du formulaire
    $sanitizedEntries['firstName'] = '';
    $sanitizedEntries['lastName'] = '';
    $sanitizedEntries['email'] = '';
}

// Ajout d'un view qui n'était pas dans le TP
require_once 'views/viewCreateNewUser.php';

    
}

function deleteCinema($managers)
{
    
// si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
if (!array_key_exists("user", $_SESSION) or $_SESSION['user'] !== 'admin@adm.adm') {
    // renvoi à la page d'accueil
    header('Location: index.php');
    exit;
}

// si la méthode de formulaire est la méthode POST
if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {

    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(INPUT_POST, ['cinemaID' => FILTER_SANITIZE_NUMBER_INT]);

    // suppression de la préférence de film
    $managers['cinemasMgr']->deleteCinema($sanitizedEntries['cinemaID']);
}
// redirection vers la liste des cinémas
header("Location: index.php?action=cinemasList");
exit;

}

function deleteFavoriteMovie($managers){
    // si l'utilisateur n'est pas connecté
if (!array_key_exists("user", $_SESSION)) {
// renvoi à la page d'accueil
    header('Location: index.php');
    exit;
}

// si la méthode de formulaire est la méthode POST
if (filter_input(INPUT_SERVER,
                'REQUEST_METHOD') === "POST") {

    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(INPUT_POST,
            ['userID' => FILTER_SANITIZE_NUMBER_INT,
        'filmID' => FILTER_SANITIZE_NUMBER_INT]);

    // suppression de la préférence de film
    $managers['prefereMgr']->deleteFavoriteMovie($sanitizedEntries['userID'],
            $sanitizedEntries['filmID']);
}
// redirection vers la liste des préférences de films
header("Location: index.php?action=editFavoriteMoviesList");
exit;
}

function deleteMovie($managers){
    // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
if (!array_key_exists("user", $_SESSION) or $_SESSION['user'] !== 'admin@adm.adm') {
    // renvoi à la page d'accueil
    header('Location: index.php');
    exit;
}

// si la méthode de formulaire est la méthode POST
if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {

    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(INPUT_POST, ['filmID' => FILTER_SANITIZE_NUMBER_INT]);

    // suppression de la préférence de film
    $managers['filmsMgr']->deleteMovie($sanitizedEntries['filmID']);
}
// redirection vers la liste des films
header("Location: index.php?action=moviesList");
exit;

}

function deleteShowtime($managers){
    // si l'utilisateur n'est pas connecté
if (!array_key_exists("user", $_SESSION)) {
// renvoi à la page d'accueil
    header('Location: index.php');
    exit;
}

// si la méthode de formulaire est la méthode POST
if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {

    // on assainie les variables
    $sanitizedEntries = filter_input_array(INPUT_POST,
            ['cinemaID' => FILTER_SANITIZE_NUMBER_INT,
        'filmID' => FILTER_SANITIZE_NUMBER_INT,
        'heureDebut' => FILTER_SANITIZE_STRING,
        'heureFin' => FILTER_SANITIZE_STRING,
        'version' => FILTER_SANITIZE_STRING,
        'from' => FILTER_SANITIZE_STRING
    ]);

    // suppression de la séance
    $managers['seanceMgr']->deleteShowtime($sanitizedEntries['cinemaID'],
            $sanitizedEntries['filmID'], $sanitizedEntries['heureDebut'],
            $sanitizedEntries['heureFin']
    );
    // en fonction d'où je viens, je redirige
    if (strstr($sanitizedEntries['from'], 'movie')) {
        header('Location: index.php?action=movieShowtimes&filmID=' . $sanitizedEntries['filmID']);
        exit;
    } else {
        header('Location: index.php?action=cinemaShowtimes&cinemaID=' . $sanitizedEntries['cinemaID']);
        exit;
    }
} else {
    // renvoi à la page d'accueil
    header('Location: index.php');
    exit;
}

}

function editCinema($managers){
    // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
if (!array_key_exists("user", $_SESSION) or $_SESSION['user'] !== 'admin@adm.adm') {
    // renvoi à la page d'accueil
    header('Location: index.php');
    exit;
}

// variable qui sert à conditionner l'affichage du formulaire
$isItACreation = false;

// si la méthode de formulaire est la méthode POST
if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {

    // on "sainifie" les entrées
    $sanEntries = filter_input_array(INPUT_POST, ['backToList' => FILTER_DEFAULT,
        'cinemaID' => FILTER_SANITIZE_NUMBER_INT,
        'adresse' => FILTER_SANITIZE_STRING,
        'denomination' => FILTER_SANITIZE_STRING,
        'modificationInProgress' => FILTER_SANITIZE_STRING]);

    // si l'action demandée est retour en arrière
    if ($sanEntries['backToList'] !== NULL) {
        // on redirige vers la page des cinémas
        header('Location: index.php?action=cinemasList');
        exit;
    }
    // sinon (l'action demandée est la sauvegarde d'un cinéma)
    else {

        // et que nous ne sommes pas en train de modifier un cinéma
        if ($sanEntries['modificationInProgress'] == NULL) {
            // on ajoute le cinéma
            $managers['cinemasMgr']->insertNewCinema($sanEntries['denomination'], $sanEntries['adresse']);
        }
        // sinon, nous sommes dans le cas d'une modification
        else {
            // mise à jour du cinéma
            $managers['cinemasMgr']->updateCinema($sanEntries['cinemaID'], $sanEntries['denomination'], $sanEntries['adresse']);
        }
        // on revient à la liste des cinémas
        header('Location: index.php?action=cinemasList');
        exit;
    }
}// si la page est chargée avec $_GET
elseif (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "GET") {
    // on "sainifie" les entrées
    $sanEntries = filter_input_array(INPUT_GET, ['cinemaID' => FILTER_SANITIZE_NUMBER_INT]);
    if ($sanEntries && $sanEntries['cinemaID'] !== NULL && $sanEntries['cinemaID'] !== '') {
        // on récupère les informations manquantes 
        $cinema = $managers['cinemasMgr']->getCinemaInformationsByID($sanEntries['cinemaID']);
    }
    // sinon, c'est une création
    else {
        $isItACreation = true;
        $cinema = [
            'CINEMAID' => '',
            'DENOMINATION' => '',
            'ADRESSE' => ''
        ];
    }
}
require_once 'views/viewEditCinema.php';
}

function moviesList($managers)
{
    
$isUserAdmin = false;

// si l'utilisateur est pas connecté et qu'il est amdinistrateur
if (array_key_exists("user", $_SESSION) and $_SESSION['user'] == 'admin@adm.adm') {
    $isUserAdmin = true;
}
require_once 'views/viewMoviesList.php';

}
