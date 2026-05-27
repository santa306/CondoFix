<?php
// crea_tabelle.php
// Eseguire con: php crea_tabelle.php (da terminale nella root del progetto)

require_once "src/Foundation/Persistent/bootstrap.php";
use Doctrine\ORM\Tools\SchemaTool;

$classes = $entityManager->getMetadataFactory()->getAllMetadata();
$schemaTool = new SchemaTool($entityManager);

try {
    // updateSchema aggiorna le tabelle esistenti senza cancellarle,
    // oppure le crea da zero se non esistono ancora.
    $schemaTool->updateSchema($classes);
    echo "Successo! Tutte le tabelle sono aggiornate nel database.\n";
} catch (Exception $e) {
    echo "Errore: " . $e->getMessage() . "\n";
}