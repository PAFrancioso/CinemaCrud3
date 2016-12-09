<?php
namespace Semeformation\Mvc\Cinema_crud\models;
use \Semeformation\Mvc\Cinema_crud\includes\DBFunctions;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class Film extends DBFunctions{
    
    /**
     * Méthode qui renvoie la liste des films
     * @return array[][]
     */
    public function getMoviesList() {
        $requete = "SELECT * FROM film";
        // on retourne le résultat
        return $this->extraireNxN($requete, null, false);
    }
    /**
     * 
     * @param type $titre
     * @param type $titreOriginal
     */
    public function insertNewMovie($titre, $titreOriginal = null) {
        // construction
        $requete = "INSERT INTO film (titre, titreOriginal) VALUES ("
                . ":titre"
                . ", :titreOriginal)";
        // exécution
        $this->executeQuery($requete,
                ['titre' => $titre,
            'titreOriginal' => $titreOriginal]);
        // log
        if ($this->logger) {
            $this->logger->info('Movie ' . $titre . ' successfully added.');
        }
    }
    /**
     * Insère une nouvelle séance pour un film donné dans un cinéma donné
     * @param integer $cinemaID
     * @param integer $filmID
     * @param datetime $dateheuredebut
     * @param datetime $dateheurefin
     * @param string $version
     */
    public function insertNewShowtime($cinemaID, $filmID, $dateheuredebut,
            $dateheurefin, $version): \PDOStatement {
        // construction
        $requete = "INSERT INTO seance (cinemaID, filmID, heureDebut, heureFin, version) VALUES ("
                . ":cinemaID"
                . ", :filmID"
                . ", :heureDebut"
                . ", :heureFin"
                . ", :version)";
        // exécution
        $resultat = $this->executeQuery($requete,
                [':cinemaID' => $cinemaID,
            ':filmID' => $filmID,
            ':heureDebut' => $dateheuredebut,
            ':heureFin' => $dateheurefin,
            ':version' => $version]);

        // log
        if ($this->logger) {
            $this->logger->info('Showtime for the movie ' . $filmID . ' at the ' . $cinemaID . ' successfully added.');
        }

        return $resultat;
    }

    /**
     * Insère une nouvelle séance pour un film donné dans un cinéma donné
     * @param integer $cinemaID
     * @param integer $filmID
     * @param datetime $dateheuredebutOld
     * @param datetime $dateheurefinOld
     * @param datetime $dateheuredebut
     * @param datetime $dateheurefin
     * @param string $version
     */
    public function updateShowtime($cinemaID, $filmID, $dateheuredebutOld,
            $dateheurefinOld, $dateheuredebut, $dateheurefin, $version): \PDOStatement {
        // construction
        $requete = "UPDATE seance SET heureDebut = :heureDebut,"
                . " heureFin = :heureFin,"
                . " version = :version"
                . " WHERE cinemaID = :cinemaID"
                . " AND filmID = :filmID"
                . " AND heureDebut = :heureDebutOld"
                . " AND heureFin = :heureFinOld";
        // exécution
        $resultat = $this->executeQuery($requete,
                [':cinemaID' => $cinemaID,
            ':filmID' => $filmID,
            ':heureDebutOld' => $dateheuredebutOld,
            ':heureFinOld' => $dateheurefinOld,
            ':heureDebut' => $dateheuredebut,
            ':heureFin' => $dateheurefin,
            ':version' => $version]);

        // log
        if ($this->logger) {
            $this->logger->info('Showtime for the movie ' . $filmID . ' at the ' . $cinemaID . ' successfully updated.');
        }

        return $resultat;
    }

    /**
     * 
     * @param type $filmID
     * @param type $titre
     * @param type $titreOriginal
     */
    public function updateMovie($filmID, $titre, $titreOriginal) {
        // on construit la requête d'insertion
        $requete = "UPDATE film SET "
                . "titre = "
                . "'" . $titre . "'"
                . ", titreOriginal = "
                . "'" . $titreOriginal . "'"
                . " WHERE filmID = "
                . $filmID;
        // exécution de la requête
        $this->executeQuery($requete);
    }

    /**
     * 
     * @param type $movieID
     */
    public function deleteMovie($movieID) {
        $this->executeQuery("DELETE FROM film WHERE filmID = "
                . $movieID);

        if ($this->logger) {
            $this->logger->info('Movie ' . $movieID . ' successfully deleted.');
        }
    }

    /**
     * Renvoie une liste de films pas encore programmés pour un cinema donné
     * @param integer $cinemaID
     * @return array
     */
    public function getNonPlannedMovies($cinemaID) {
        // requête de récupération des titres et des identifiants des films
        // qui n'ont pas encore été programmés dans ce cinéma
        $requete = "SELECT f.filmID, f.titre "
                . "FROM film f"
                . " WHERE f.filmID NOT IN ("
                . "SELECT filmID"
                . " FROM seance"
                . " WHERE cinemaID = :id"
                . ")";
        // extraction de résultat
        $resultat = $this->extraireNxN($requete, ['id' => $cinemaID], false);
        // retour du résultat
        return $resultat;
    }

     /**
     * 
     * @param type $cinemaID
     * @param type $filmID
     * @return type
     */
    public function getMovieShowtimes($cinemaID, $filmID) {
        // requête qui permet de récupérer la liste des séances d'un film donné dans un cinéma donné
        $requete = "SELECT s.* FROM seance s"
                . " WHERE s.filmID = " . $filmID
                . " AND s.cinemaID = " . $cinemaID;
        // on extrait les résultats
        $resultat = $this->extraireNxN($requete);
        // on retourne la requête
        return $resultat;
    }
    
    
     /**
     * Supprime une séance pour un film donné et un cinéma donné
     * @param type $cinemaID
     * @param type $filmID
     * @param type $heureDebut
     * @param type $heureFin
     */
    public function deleteShowtime($cinemaID, $filmID, $heureDebut, $heureFin) {
        $this->executeQuery("DELETE FROM seance "
                . "WHERE cinemaID = :cinemaID "
                . "AND filmID = :filmID "
                . "AND heureDebut = :heureDebut"
                . " AND heureFin = :heureFin",
                [':cinemaID' => $cinemaID,
            ':filmID' => $filmID,
            ':heureDebut' => $heureDebut,
            ':heureFin' => $heureFin]);

        if ($this->logger) {
            $this->logger->info('Showtime for the movie ' . $filmID . ' and the cinema ' . $cinemaID . ' successfully deleted.');
        }
    }
    
}