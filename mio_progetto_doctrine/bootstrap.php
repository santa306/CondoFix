<?php
// bootstrap.php

require_once "vendor/autoload.php";

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

// 1. Diciamo a Doctrine dove trovare le nostre Entità (la cartella src)
$paths = [__DIR__."/src"];
$isDevMode = true;

// Configurazione per leggere gli Attributi di PHP 8
$config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);

// 2. Configurazione del Database per XAMPP
$dbParams = [
    'driver'   => 'pdo_mysql',
    'user'     => 'root',           // Utente di default di XAMPP
    'password' => '',               // XAMPP di default non ha password
    'dbname'   => 'mio_database',   // Il nome del database che creeremo tra poco
];

$connection = DriverManager::getConnection($dbParams, $config);

// 3. Creiamo l'EntityManager (il "direttore d'orchestra" di Doctrine)
$entityManager = new EntityManager($connection, $config);