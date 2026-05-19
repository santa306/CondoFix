<?php
// src/Completato.php

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Completato extends Accettato
{
    // Anche qui, salviamo il nome o il percorso del file della fattura
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $fattura = null;

    // --- GETTER E SETTER ---
    public function getFattura(): ?string { return $this->fattura; }
    public function setFattura(?string $fattura): void { $this->fattura = $fattura; }
}