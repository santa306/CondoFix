<?php
// src/Model/Condomino.php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'condomini_residenti')] // Uso un nome diverso per non confonderlo con i palazzi
class Condomino
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id = null;

    #[ORM\Column(type: 'string')]
    private string $nome;

    #[ORM\Column(type: 'string')]
    private string $cognome;

    #[ORM\Column(type: 'string', unique: true)]
    private string $email;

    #[ORM\Column(type: 'string')]
    private string $telefono;

    // Relazione: Molti Condomini (persone) vivono in 1 Condominio (palazzo)
    #[ORM\ManyToOne(targetEntity: Condominio::class)]
    private Condominio|null $condominio = null;

    // --- GETTER E SETTER ---
    public function getId(): ?int { return $this->id; }

    public function getNome(): string { return $this->nome; }
    public function setNome(string $nome): void { $this->nome = $nome; }

    public function getCognome(): string { return $this->cognome; }
    public function setCognome(string $cognome): void { $this->cognome = $cognome; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }

    public function getTelefono(): string { return $this->telefono; }
    public function setTelefono(string $telefono): void { $this->telefono = $telefono; }

    public function getCondominio(): ?Condominio { return $this->condominio; }
    public function setCondominio(?Condominio $condominio): void { $this->condominio = $condominio; }
}