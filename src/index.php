<?php


require_once __DIR__ . '/vendor/autoload.php';

// init des managers
require_once __DIR__ . './includes/Manager.php';

// initialisation de l'application
require_once __DIR__ . './init.php';

// Appel au controleur serviteur
require_once __DIR__ . './controllers/controleur.php';

session_start();

// on "sainifie" les entrées
$sanitizedEntries = filter_input_array(INPUT_GET,
        ['action' => FILTER_SANITIZE_STRING]);
if ($sanitizedEntries && $sanitizedEntries['action'] !== '') 
    {
    
    // si l'action demandée est la liste des cinémas
    if ($sanitizedEntries['action'] == "cinemasList") {
    
        // Activation de la route cinemasList
        cinemasList($managers); 
    } 
    
    elseif ($sanitizedEntries['action'] == "cinemaShowtimes") {
    cinemaShowtimes($managers);
    }
    
    elseif ($sanitizedEntries['action'] == "createNewUser") {
        createNewUser($managers);
    }
    
    elseif ($sanitizedEntries['action'] == "deleteCinema") {
        deleteCinema($managers);
    }
    
    elseif ($sanitizedEntries['action'] == "deleteFavoriteMovie") {
        deleteFavoriteMovie($managers);
    }
    
    elseif ($sanitizedEntries['action'] == "deleteMovie") {
        deleteMovie($managers);
    }
    
    elseif ($sanitizedEntries['action'] == "deleteShowtime") {
        deleteShowtime($managers);
    }
    
    elseif ($sanitizedEntries['action'] == "editCinema") {
        editCinema($managers);
    }
    
    elseif ($sanitizedEntries['action'] == "editFavoriteMovie") {
        editFavoriteMovie($managers);
    }
    
    elseif ($sanitizedEntries['action'] == "editFavoriteMoviesList") {
        editFavoriteMoviesList($managers);
    }
    
    elseif ($sanitizedEntries['action'] == "editMovie") {
        editMovie($managers);
    }
       
    elseif ($sanitizedEntries['action'] == "editShowtime") {
        editShowtime($managers);
    }
    
    elseif ($sanitizedEntries['action'] == "logout") {
        logout();
    }
    elseif ($sanitizedEntries['action'] == "moviesList") {
        moviesList($managers);
    }
    
    elseif ($sanitizedEntries['action'] == "movieShowtimes") {
        movieShowtimes($managers);
    }
    
    else {
        // Activation de la route par défaut (page d'accueil)
        home($managers);
    }
    
} else {
    // Activation de la route par défaut (page d'accueil)
    home($managers);
}
?>

