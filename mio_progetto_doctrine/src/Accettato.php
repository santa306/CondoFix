<?php
// src/Accettato.php

require_once 'Stato.php'; // Importiamo il "padre"

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Accettato extends Stato
{
    // Nel tuo diagramma, l'Accettato ha una "priorità"
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $priorita = null; // Es: "Alta", "Media", "Bassa"

    // Relazione: 1 stato "Accettato" è assegnato a 1 Fornitore
    #[ORM\ManyToOne(targetEntity: Fornitore::class)]
    private Fornitore|null $fornitore = null;

    // --- GETTER E SETTER ---
    public function getPriorita(): ?string { return $this->priorita; }
    public function setPriorita(?string $priorita): void { $this->priorita = $priorita; }

    public function getFornitore(): ?Fornitore { return $this->fornitore; }
    public function setFornitore(?Fornitore $fornitore): void { $this->fornitore = $fornitore; }
}