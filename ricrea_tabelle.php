<?php
// ricrea_tabelle.php
// Cancella TUTTE le tabelle esistenti e le ricrea da zero.
// ATTENZIONE: cancella tutti i dati! Va bene in fase di sviluppo.

require_once "bootstrap.php";
use Doctrine\ORM\Tools\SchemaTool;

$classes = $entityManager->getMetadataFactory()->getAllMetadata();
$schemaTool = new SchemaTool($entityManager);

try {
    // Prima cancella le tabelle vecchie
    $schemaTool->dropSchema($classes);
    echo "Tabelle vecchie eliminate.\n";

    // Poi ricrea le tabelle nuove
    $schemaTool->createSchema($classes);
    echo "Tabelle nuove create con successo.\n";
} catch (Exception $e) {
    echo "Errore: " . $e->getMessage() . "\n";
}