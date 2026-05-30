<?php
// src/Model/Nota.php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'note')]
class Nota
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id = null;

    // Il testo della nota (può essere lungo, usiamo 'text')
    #[ORM\Column(type: 'text')]
    private string $testo;

    // Data e ora in cui la nota è stata scritta
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface|null $timestamp = null;

    // Ogni nota appartiene a 1 Intervento (molte note per un intervento)
    #[ORM\ManyToOne(targetEntity: Intervento::class, inversedBy: 'note')]
    private Intervento|null $intervento = null;

    // --- GETTER E SETTER ---
    public function getId(): ?int { return $this->id; }

    public function getTesto(): string { return $this->testo; }
    public function setTesto(string $testo): void { $this->testo = $testo; }

    public function getTimestamp(): ?\DateTimeInterface { return $this->timestamp; }
    public function setTimestamp(?\DateTimeInterface $timestamp): void { $this->timestamp = $timestamp; }

    public function getIntervento(): ?Intervento { return $this->intervento; }
    public function setIntervento(?Intervento $intervento): void { $this->intervento = $intervento; }
}