<?php
// src/Entity/Presentato.php
// Stato iniziale: il condomino ha inviato la segnalazione.
// L'amministratore deve ancora valutarla (approvarla o rifiutarla).

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Presentato extends Stato
{
    public function getTipo(): string { return 'presentato'; }
}
