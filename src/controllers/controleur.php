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
            header("Location: editFavoriteMoviesList.php");
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
        header("Location: editFavoriteMoviesList.php");
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

function moviesList($managers)
{
    
$isUserAdmin = false;

// si l'utilisateur est pas connecté et qu'il est amdinistrateur
if (array_key_exists("user", $_SESSION) and $_SESSION['user'] == 'admin@adm.adm') {
    $isUserAdmin = true;
}
require_once 'views/viewMoviesList.php';

}


