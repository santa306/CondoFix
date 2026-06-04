<?php
// src/Entity/Nota.php
// Nota operativa aggiunta dal Fornitore durante l'esecuzione di un intervento.
// Ogni nota ha un testo, un timestamp automatico e appartiene a un Intervento.
//
// FIX rispetto al codice originale:
//   - La classe era completamente vuota.
//   - La nota è collegata a Intervento (non a InCorso):
//     rimane accessibile anche quando lo stato diventa Completato.

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'note')]
class Nota
{
    // -------------------------------------------------------
    // ATTRIBUTI
    // -------------------------------------------------------

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id = null;

    // Testo della nota operativa inserita dal fornitore
    #[ORM\Column(type: 'text')]
    private string $testo;

    // Timestamp impostato automaticamente alla creazione
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $timestamp;

    // Ogni nota appartiene a 1 Intervento
    // (molte note -> 1 intervento)
    #[ORM\ManyToOne(targetEntity: Intervento::class, inversedBy: 'note')]
    #[ORM\JoinColumn(nullable: false)]
    private Intervento|null $intervento = null;

    // -------------------------------------------------------
    // COSTRUTTORE
    // Imposta automaticamente il timestamp alla creazione
    // -------------------------------------------------------

    public function __construct()
    {
        $this->timestamp = new \DateTime();
    }

    // -------------------------------------------------------
    // GETTER E SETTER
    // -------------------------------------------------------

    public function getId(): ?int { return $this->id; }

    public function getTesto(): string        { return $this->testo; }
    public function setTesto(string $v): void { $this->testo = $v; }

    public function getTimestamp(): \DateTimeInterface { return $this->timestamp; }

    public function getIntervento(): ?Intervento  { return $this->intervento; }
    public function setIntervento(?Intervento $v): void { $this->intervento = $v; }
}
