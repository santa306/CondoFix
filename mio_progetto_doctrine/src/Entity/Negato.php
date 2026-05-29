<?php
// src/Entity/Negato.php
// L'amministratore ha valutato la segnalazione e ha deciso di NON procedere.
// Stato terminale: l'intervento non verrà eseguito.

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Negato extends Stato
{
    // Motivo opzionale del rifiuto
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $motivazione = null;

    // -------------------------------------------------------
    // GETTER E SETTER
    // -------------------------------------------------------

    public function getMotivazione(): ?string    { return $this->motivazione; }
    public function setMotivazione(?string $v): void { $this->motivazione = $v; }

    public function getTipo(): string { return 'negato'; }
}
