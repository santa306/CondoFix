<?php
// src/Model/Negato.php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Negato extends Stato
{
    // Eredita solo $id e $nome da Stato.
    // Nessun campo aggiuntivo: lo stato "Negato" indica solo che la richiesta è stata rifiutata.
}