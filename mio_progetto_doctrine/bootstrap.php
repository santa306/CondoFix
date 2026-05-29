<?php
// bootstrap.php
// Configurazione Doctrine ORM 3.x (compatibile con il setup esistente del progetto).
// Crea la variabile globale $entityManager usata da PersistentManager.

require_once __DIR__ . "/vendor/autoload.php";

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

// 1. Percorso delle Entity (ora in src/Entity)
$paths = [__DIR__ . "/src/Entity"];
$isDevMode = true;

// Configurazione per leggere gli Attributi PHP 8 (#[ORM\...])
$config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);

// 2. Parametri database (XAMPP)
$dbParams = [
    'driver'   => 'pdo_mysql',
    'user'     => 'root',
    'password' => '',
    'dbname'   => 'mio_database',   // stesso DB del progetto esistente
];

$connection = DriverManager::getConnection($dbParams, $config);

// 3. EntityManager (Doctrine 3.x: si passa connection + config)
$entityManager = new EntityManager($connection, $config);
