<?php
// src/Intervento.php

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'interventi')]
class Intervento
{
    // ------------------------------------
    // 1. VARIABILI
    // ------------------------------------
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id = null;

    #[ORM\Column(type: 'string')]
    private string $titolo;

    // Uso 'text' invece di 'string' perché la descrizione può essere molto lunga (es. più di 255 caratteri)
    #[ORM\Column(type: 'text')]
    private string $descrizione;

    // Per le foto di solito si salva nel database solo il NOME del file o il PERCORSO (es. "foto1.jpg"), quindi basta una stringa
    // nullable: true perché magari all'inizio l'intervento non ha ancora una foto allegata
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $foto = null;

    // --- LA RELAZIONE CON IL CONDOMINIO ---
    // Molti Interventi appartengono a 1 Condominio
    #[ORM\ManyToOne(targetEntity: Condominio::class)]
    private Condominio|null $condominio = null;

    // --- LA RELAZIONE CON LO STATO (1 a 1) ---
    #[ORM\OneToOne(targetEntity: Stato::class)]
    private Stato|null $stato = null;

    // ------------------------------------
    // 2. GETTER E SETTER
    // ------------------------------------
    public function getId(): ?int { return $this->id; }

    public function getTitolo(): string { return $this->titolo; }
    public function setTitolo(string $titolo): void { $this->titolo = $titolo; }

    public function getDescrizione(): string { return $this->descrizione; }
    public function setDescrizione(string $descrizione): void { $this->descrizione = $descrizione; }

    public function getFoto(): ?string { return $this->foto; }
    public function setFoto(?string $foto): void { $this->foto = $foto; }

    public function getCondominio(): ?Condominio { return $this->condominio; }
    public function setCondominio(?Condominio $condominio): void { $this->condominio = $condominio; }

    // Getter e Setter per lo Stato
    public function getStato(): ?Stato { return $this->stato; }
    public function setStato(?Stato $stato): void { $this->stato = $stato; }
}