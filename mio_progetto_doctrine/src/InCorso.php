<?php
// src/InCorso.php

require_once 'Accettato.php';

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class InCorso extends Accettato
{
    // Adesso questa classe è vuota!
    // Eredita semplicemente la priorità e il fornitore dalla classe "Accettato"
    // e verrà collegata alle "Note", ma non ha più la colonna preventivo.
}