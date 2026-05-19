<?php
// src/Condominio.php

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'condomini')]
class Condominio
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id = null;

    #[ORM\Column(type: 'string')]
    private string $nome;

    #[ORM\Column(type: 'string')]
    private string $indirizzo;

    #[ORM\Column(type: 'string')]
    private string $citta;

    // --- GETTER E SETTER ---
    public function getId(): ?int { return $this->id; }
    public function getNome(): string { return $this->nome; }
    public function setNome(string $nome): void { $this->nome = $nome; }
    public function getIndirizzo(): string { return $this->indirizzo; }
    public function setIndirizzo(string $indirizzo): void { $this->indirizzo = $indirizzo; }
    public function getCitta(): string { return $this->citta; }
    public function setCitta(string $citta): void { $this->citta = $citta; }

    

    // ECCO LA RELAZIONE! (Molti condomini -> 1 Amministratore)
    #[ORM\ManyToOne(targetEntity: Amministratore::class)]
    private Amministratore|null $amministratore = null;

    // E i suoi getter/setter
    public function getAmministratore(): ?Amministratore { return $this->amministratore; }
    public function setAmministratore(?Amministratore $amministratore): void { $this->amministratore = $amministratore; }
}