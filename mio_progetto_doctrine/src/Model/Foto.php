<?php
// src/Model/Foto.php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'foto')]
class Foto
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id = null;

    // Percorso o nome del file (es. "uploads/foto1.jpg")
    #[ORM\Column(type: 'string')]
    private string $percorso;

    // Ogni foto appartiene a 1 Intervento
    #[ORM\ManyToOne(targetEntity: Intervento::class, inversedBy: 'foto')]
    private Intervento|null $intervento = null;

    // --- GETTER E SETTER ---
    public function getId(): ?int { return $this->id; }

    public function getPercorso(): string { return $this->percorso; }
    public function setPercorso(string $percorso): void { $this->percorso = $percorso; }

    public function getIntervento(): ?Intervento { return $this->intervento; }
    public function setIntervento(?Intervento $intervento): void { $this->intervento = $intervento; }
}