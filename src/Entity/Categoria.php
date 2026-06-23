<?php
// src/Entity/Categoria.php
// Tipologia di specializzazione dei Fornitori.
// Es: "Idraulico", "Elettricista", "Muratore", "Falegname"

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'categorie')]
class Categoria
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id = null;

    #[ORM\Column(type: 'string', unique: true)]
    private string $nome;

    // -------------------------------------------------------
    // GETTER E SETTER
    // -------------------------------------------------------

    public function getId(): ?int            { return $this->id; }

    public function getNome(): string        { return $this->nome; }
    public function setNome(string $v): void { $this->nome = $v; }
}
