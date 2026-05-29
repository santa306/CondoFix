<?php
// crea_tabelle.php
// Genera tutte le tabelle nel database a partire dalle Entity.
// Eseguire una volta sola (o dopo aver cancellato il DB):
//   php crea_tabelle.php

require_once "bootstrap.php";
use Doctrine\ORM\Tools\SchemaTool;

// Legge i metadati di tutte le Entity in src/Entity
$classes = $entityManager->getMetadataFactory()->getAllMetadata();

$schemaTool = new SchemaTool($entityManager);

try {
    $schemaTool->createSchema($classes);
    echo "Tabelle create con successo nel database 'mio_database'.\n";
} catch (Exception $e) {
    echo "Errore: " . $e->getMessage() . "\n";
}
