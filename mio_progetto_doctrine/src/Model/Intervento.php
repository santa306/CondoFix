<?php
// src/Model/Intervento.php

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'interventi')]
class Intervento
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id = null;

    #[ORM\Column(type: 'string')]
    private string $titolo;

    // 'text' perché la descrizione può superare i 255 caratteri
    #[ORM\Column(type: 'text')]
    private string $descrizione;

    // Chi ha fatto la segnalazione (il Condomino residente)
    #[ORM\ManyToOne(targetEntity: Condomino::class)]
    private Condomino|null $autoreSegnalazione = null;

    // Relazione con il Condominio (palazzo): molti interventi per 1 condominio
    #[ORM\ManyToOne(targetEntity: Condominio::class)]
    private Condominio|null $condominio = null;

    // Stato corrente dell'intervento (1 a 1)
    #[ORM\OneToOne(targetEntity: Stato::class)]
    private Stato|null $stato = null;

    // Un intervento può avere più foto
    #[ORM\OneToMany(targetEntity: Foto::class, mappedBy: 'intervento', cascade: ['persist', 'remove'])]
    private Collection $foto;

    // Un intervento può avere più note
    #[ORM\OneToMany(targetEntity: Nota::class, mappedBy: 'intervento', cascade: ['persist', 'remove'])]
    private Collection $note;

    public function __construct()
    {
        $this->foto = new ArrayCollection();
        $this->note = new ArrayCollection();
    }

    // --- GETTER E SETTER ---
    public function getId(): ?int { return $this->id; }

    public function getTitolo(): string { return $this->titolo; }
    public function setTitolo(string $titolo): void { $this->titolo = $titolo; }

    public function getDescrizione(): string { return $this->descrizione; }
    public function setDescrizione(string $descrizione): void { $this->descrizione = $descrizione; }

    public function getAutoreSegnalazione(): ?Condomino { return $this->autoreSegnalazione; }
    public function setAutoreSegnalazione(?Condomino $autoreSegnalazione): void { $this->autoreSegnalazione = $autoreSegnalazione; }

    public function getCondominio(): ?Condominio { return $this->condominio; }
    public function setCondominio(?Condominio $condominio): void { $this->condominio = $condominio; }

    public function getStato(): ?Stato { return $this->stato; }
    public function setStato(?Stato $stato): void { $this->stato = $stato; }

    public function getFoto(): Collection { return $this->foto; }
    public function addFoto(Foto $foto): void
    {
        if (!$this->foto->contains($foto)) {
            $this->foto->add($foto);
            $foto->setIntervento($this);
        }
    }

    public function getNote(): Collection { return $this->note; }
    public function addNota(Nota $nota): void
    {
        if (!$this->note->contains($nota)) {
            $this->note->add($nota);
            $nota->setIntervento($this);
        }
    }
}