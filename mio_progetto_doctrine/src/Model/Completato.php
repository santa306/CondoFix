<?php
// src/Model/Completato.php

namespace App\Model;

require_once 'Stato.php';

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Completato extends Stato
{
    // Priorità assegnata (stessa logica di Accettato e InCorso)
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $priorita = null;

    // Fornitore assegnato per questo intervento
    #[ORM\ManyToOne(targetEntity: Fornitore::class)]
    private Fornitore|null $fornitore = null;

    // RELAZIONE UNO-A-UNO: Ogni stato completato può avere collegata 1 Fattura specifica
    // cascade: ['persist', 'remove'] serve per salvare o eliminare la fattura insieme allo stato
    #[ORM\OneToOne(targetEntity: Fattura::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'fattura_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private Fattura|null $fattura = null;

    // --- GETTER E SETTER ---
    public function getPriorita(): ?string { return $this->priorita; }
    public function setPriorita(?string $priorita): void { $this->priorita = $priorita; }

    public function getFornitore(): ?Fornitore { return $this->fornitore; }
    public function setFornitore(?Fornitore $fornitore): void { $this->fornitore = $fornitore; }

    public function getFattura(): ?Fattura { return $this->fattura; }
    public function setFattura(?Fattura $fattura): void { $this->fattura = $fattura; }
}