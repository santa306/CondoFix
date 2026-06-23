<?php
// src/Entity/Condominio.php
// Rappresenta un condominio fisico gestito da un Amministratore.

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
    private string $nome;        // Es. "Condominio Centrale"

    #[ORM\Column(type: 'string')]
    private string $indirizzo;   // Es. "Via Roma 10"

    #[ORM\Column(type: 'string')]
    private string $citta;       // Es. "Milano"

    // Ogni condominio è gestito da 1 Amministratore
    // (molti condomini -> 1 amministratore)
    #[ORM\ManyToOne(targetEntity: Amministratore::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Amministratore|null $amministratore = null;

    // -------------------------------------------------------
    // GETTER E SETTER
    // -------------------------------------------------------

    public function getId(): ?int            { return $this->id; }

    public function getNome(): string        { return $this->nome; }
    public function setNome(string $v): void { $this->nome = $v; }

    public function getIndirizzo(): string        { return $this->indirizzo; }
    public function setIndirizzo(string $v): void { $this->indirizzo = $v; }

    public function getCitta(): string        { return $this->citta; }
    public function setCitta(string $v): void { $this->citta = $v; }

    public function getAmministratore(): ?Amministratore  { return $this->amministratore; }
    public function setAmministratore(?Amministratore $v): void { $this->amministratore = $v; }
}
