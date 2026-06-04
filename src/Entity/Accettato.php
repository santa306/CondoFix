<?php
// src/Entity/Accettato.php
// L'amministratore ha approvato la segnalazione, assegnato una priorità
// e un fornitore. Il lavoro è pronto per essere avviato.
// priorita e fornitore sono ereditati da Stato.

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Accettato extends Stato
{
    public function getTipo(): string { return 'accettato'; }
}
