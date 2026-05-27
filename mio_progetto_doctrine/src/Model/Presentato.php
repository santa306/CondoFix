<?php
// src/Model/Presentato.php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Presentato extends Stato
{
    // Eredita solo $id e $nome da Stato.
    // Nessun campo aggiuntivo: lo stato "Presentato" è solo una segnalazione iniziale.
}