<?php
// src/Entity/Amministratore.php
// Gestisce i condomini assegnatigli e approva/rifiuta gli interventi.

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Amministratore extends Utente
{
    // Attributo specifico dell'amministratore
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $telefono = null;

    // -------------------------------------------------------
    // GETTER E SETTER
    // -------------------------------------------------------

    public function getTelefono(): ?string    { return $this->telefono; }
    public function setTelefono(?string $v): void { $this->telefono = $v; }
}
