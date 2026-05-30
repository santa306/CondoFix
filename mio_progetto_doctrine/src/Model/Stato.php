<?php
// src/Model/Stato.php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'stati')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'tipo', type: 'string')]
#[ORM\DiscriminatorMap([
    'presentato' => Presentato::class,
    'accettato'  => Accettato::class,
    'in_corso'   => InCorso::class,
    'completato' => Completato::class,
    'negato'     => Negato::class,
])]
abstract class Stato
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected int|null $id = null;

    // Il nome/etichetta dello stato (es. "Presentato", "Accettato", ecc.)
    #[ORM\Column(type: 'string')]
    protected string $nome;

    // --- GETTER E SETTER ---
    public function getId(): ?int { return $this->id; }

    public function getNome(): string { return $this->nome; }
    public function setNome(string $nome): void { $this->nome = $nome; }
}