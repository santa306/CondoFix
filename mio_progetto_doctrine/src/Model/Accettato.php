<?php
// src/Model/Accettato.php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Accettato extends Stato
{
    // Priorità assegnata dal condominio (es. "Alta", "Media", "Bassa")
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $priorita = null;

    // Fornitore assegnato per questo intervento
    #[ORM\ManyToOne(targetEntity: Fornitore::class)]
    private Fornitore|null $fornitore = null;

    // --- GETTER E SETTER ---
    public function getPriorita(): ?string { return $this->priorita; }
    public function setPriorita(?string $priorita): void { $this->priorita = $priorita; }

    public function getFornitore(): ?Fornitore { return $this->fornitore; }
    public function setFornitore(?Fornitore $fornitore): void { $this->fornitore = $fornitore; }
}