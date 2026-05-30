<?php
// src/Model/Fattura.php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'fatture')]
class Fattura
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id = null;

    // Il nome del file o il percorso di memorizzazione (es. "uploads/fattura_123.pdf")
    #[ORM\Column(type: 'string')]
    private string $percorsoFile;

    // L'importo totale della fattura (es. 150.50)
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $importo;

    // La data di emissione del documento
    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $dataEmissione;

    // --- GETTER E SETTER ---
    public function getId(): ?int { return $this->id; }

    public function getPercorsoFile(): string { return $this->percorsoFile; }
    public function setPercorsoFile(string $percorsoFile): void { $this->percorsoFile = $percorsoFile; }

    public function getImporto(): float { return $this->importo; }
    public function setImporto(float $importo): void { $this->importo = $importo; }

    public function getDataEmissione(): \DateTimeInterface { return $this->dataEmissione; }
    public function setDataEmissione(\DateTimeInterface $dataEmissione): void { $this->dataEmissione = $dataEmissione; }
}