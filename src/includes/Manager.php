<?php

use Semeformation\Mvc\Cinema_crud\models\Utilisateur;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Création du logger
$logger = new Logger("Functions");
$logger->pushHandler(new StreamHandler(dirname(__DIR__) . './logs/functions.log'));
// Appel de la classe Utilisateur qui herite de DBFunctions
$utilisateursMgr = new Utilisateur($logger);
