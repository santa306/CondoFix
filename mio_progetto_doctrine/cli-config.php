<?php
// cli-config.php
require_once "bootstrap.php";

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

return ConsoleRunner::createApplication(
    new SingleManagerProvider($entityManager)
);