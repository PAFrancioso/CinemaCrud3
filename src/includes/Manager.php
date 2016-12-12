<?php

use Semeformation\Mvc\Cinema_crud\models\Utilisateur;
use Semeformation\Mvc\Cinema_crud\models\Cinema;
use Semeformation\Mvc\Cinema_crud\models\Film;
use Semeformation\Mvc\Cinema_crud\models\Prefere;
use Semeformation\Mvc\Cinema_crud\models\Seance;


use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Création du logger
$logger = new Logger("Functions");
$logger->pushHandler(new StreamHandler(dirname(__DIR__) . './logs/functions.log'));
// Appel de la classe Utilisateur qui herite de DBFunctions

//$utilisateursMgr = new Utilisateur($logger);
//$cinemasMgr = new Cinema($logger);
//$filmsMgr = new Film($logger);
//$prefereMgr = new Prefere($logger);
//$seanceMgr = new Seance($logger);
$managers = ['utilisateursMgr' => new Utilisateur($logger),
'cinemasMgr' => new Cinema($logger),
'seanceMgr' => new Seance($logger),
'prefereMgr' => new Prefere($logger),
'filmsMgr' => new Film($logger)];