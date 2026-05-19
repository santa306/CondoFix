<?php
// src/Categoria.php

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'categorie')]
class Categoria
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id = null;

    #[ORM\Column(type: 'string')]
    private string $nome; // Es: "Idraulico", "Elettricista", "Muratore"

    // --- GETTER E SETTER ---
    public function getId(): ?int { return $this->id; }

    public function getNome(): string { return $this->nome; }
    public function setNome(string $nome): void { $this->nome = $nome; }
}