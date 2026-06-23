<?php
// cli-config.php
// Configurazione per i comandi Doctrine da terminale (Doctrine 3.x).
// Eseguire dalla cartella del progetto, es:
//   php vendor/bin/doctrine orm:schema-tool:create
//   php vendor/bin/doctrine orm:schema-tool:update --force
//   php vendor/bin/doctrine orm:validate-schema

require_once __DIR__ . "/bootstrap.php";

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

return ConsoleRunner::createApplication(
    new SingleManagerProvider($entityManager)
);
