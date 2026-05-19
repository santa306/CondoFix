<?php
// src/Fornitore.php

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'fornitori')]
class Fornitore
{
    // ------------------------------------
    // 1. VARIABILI
    // ------------------------------------
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id = null;

    #[ORM\Column(type: 'string')]
    private string $nome; // Nome dell'azienda o del professionista

    #[ORM\Column(type: 'string')]
    private string $telefono;

    // L'email potrebbe essere vuota se è un vecchio artigiano senza internet! Quindi mettiamo nullable: true
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $partitaIva = null;

    // --- LA RELAZIONE CON LA CATEGORIA ---
    #[ORM\ManyToOne(targetEntity: Categoria::class)]
    private Categoria|null $categoria = null;


    // ------------------------------------
    // 2. GETTER E SETTER
    // ------------------------------------
    public function getId(): ?int { return $this->id; }

    public function getNome(): string { return $this->nome; }
    public function setNome(string $nome): void { $this->nome = $nome; }

    public function getTelefono(): string { return $this->telefono; }
    public function setTelefono(string $telefono): void { $this->telefono = $telefono; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): void { $this->email = $email; }

    public function getPartitaIva(): ?string { return $this->partitaIva; }
    public function setPartitaIva(?string $partitaIva): void { $this->partitaIva = $partitaIva; }

    public function getCategoria(): ?Categoria { return $this->categoria; }
    public function setCategoria(?Categoria $categoria): void { $this->categoria = $categoria; }
}