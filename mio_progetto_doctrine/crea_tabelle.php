<?php
// crea_tabelle.php

require_once "bootstrap.php";
use Doctrine\ORM\Tools\SchemaTool;

// 1. Diciamo a Doctrine di leggere tutti i file dentro "src" (come il tuo User.php)
$classes = $entityManager->getMetadataFactory()->getAllMetadata();

// 2. Prepariamo lo strumento che materialmente crea le tabelle
$schemaTool = new SchemaTool($entityManager);

try {
    // 3. Creiamo le tabelle nel database!
    $schemaTool->createSchema($classes);
    echo "Magia riuscita! Tabella 'users' creata nel database con successo.\n";
} catch (Exception $e) {
    echo "C'è stato un problema: " . $e->getMessage() . "\n";
}