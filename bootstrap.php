<?php
// bootstrap.php
// Configurazione Doctrine ORM 3.x.
// Crea la variabile globale $entityManager usata da PersistentManager.

//require_once uno per richiesta http
require_once __DIR__ . "/vendor/autoload.php";
//driverManager apre la connessione
use Doctrine\DBAL\DriverManager; //DBAL: Database abstracion layer= strato + basso parla con mysql
use Doctrine\ORM\EntityManager; //ORM: Object-Relational Mapping: strato alto= mappa gli oggetti e tabelle
use Doctrine\ORM\ORMSetup;
//ENtityManager e ORMSetup vinono in ORM


// 1. Percorso delle Entity (ora in src/Entity)
$paths = [__DIR__ . "/src/Entity"]; //Dice dove sono le entity Ã¨un array perhÃ¨ possono essere sparse qua e lÃ  entity
$isDevMode = true; //entri in modalitÃ  sviluppatore facendo si che non si usi la cache dei metadati per rileggere ogni volta i mapping su richiesta, Ã¨ piu lento

// Configurazione per leggere gli Attributi PHP 8 (#[ORM\...])
$config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);//come leggerne il mapping
// Cartella scrivibile per i proxy di Doctrine (su hosting come InfinityFree /tmp non e' scrivibile).
$config->setProxyDir(__DIR__ . '/tmp');
$config->setProxyNamespace('Proxies');
//$paths, $isDevMode ti dice dove cercare e se attivare la cache


// 2. Parametri database (XAMPP)
$dbParams = [
    'driver'   => 'pdo_mysql',
    'user'     => 'root',
    'password' => '',
    'dbname'   => 'mio_database',
];
//s icrea l'entita che vive in DBAL
$connection = DriverManager::getConnection($dbParams, $config);//non apre subito la connessione tcp a mySql, si apre solo alla prima query

// 3. EntityManager (Doctrine 3.x: si passa connection per parlare con il db + config per sapere come mappare)
$entityManager = new EntityManager($connection, $config);//variabile globale ovviamente

