<?php
// src/Presentato.php

require_once 'Stato.php';

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Presentato extends Stato
{
    // Vuoto, perché eredita solo $id e $nome dal padre Stato!
}